<?php

namespace App\Services;

use App\Models\File;
use App\Models\UploadSession;
use App\Models\User;
use App\Models\Folder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Exception;

class ResumableUploadService
{
    public function initialize(array $data, User $user, string $role = 'user'): UploadSession
    {
        $this->validateFilename($data['filename']);
        
        $maxFileSize = config('resumable_uploads.max_file_size', 5368709120);
        if ($data['filesize'] <= 0 || $data['filesize'] > $maxFileSize) {
            throw new InvalidArgumentException("Taille de fichier invalide.");
        }

        if (!empty($data['folder_id'])) {
            $folder = Folder::find($data['folder_id']);
            if (!$folder) {
                throw new InvalidArgumentException("Dossier introuvable.");
            }
            if ($role === 'user' && $folder->user_id !== $user->id) {
                throw new InvalidArgumentException("Vous n'avez pas accès à ce dossier.");
            }
        }

        $uuid = Str::uuid()->toString();
        $chunkSize = $data['chunk_size'] ?? config('resumable_uploads.chunk_size', 8388608);
        $totalChunks = ceil($data['filesize'] / $chunkSize);

        $maxChunks = config('resumable_uploads.max_chunks', 1000);
        if ($totalChunks > $maxChunks) {
            throw new InvalidArgumentException("Le fichier nécessite trop de morceaux.");
        }

        $extension = strtolower(pathinfo($data['filename'], PATHINFO_EXTENSION));
        $disk = config('resumable_uploads.disk', 'private');
        $tempDir = config('resumable_uploads.temporary_directory', '.uploads');
        $tempPath = "{$tempDir}/{$uuid}/chunks";

        Storage::disk($disk)->makeDirectory($tempPath);

        return UploadSession::create([
            'uuid' => $uuid,
            'user_id' => $user->id,
            'folder_id' => $data['folder_id'] ?? null,
            'original_name' => $data['filename'],
            'extension' => $extension ?: null,
            'mime_type' => $data['mime_type'] ?? null,
            'total_size' => $data['filesize'],
            'chunk_size' => $chunkSize,
            'total_chunks' => $totalChunks,
            'status' => 'pending',
            'disk' => $disk,
            'temporary_path' => $tempPath,
            'checksum' => $data['checksum'] ?? null,
            'last_activity_at' => now(),
        ]);
    }

    public function storeChunk(UploadSession $session, UploadedFile $chunk, int $chunkIndex, ?string $checksum = null): array
    {
        if (!$session->isActive()) {
            throw new InvalidArgumentException("La session n'est plus active.");
        }

        if ($chunkIndex < 0 || $chunkIndex >= $session->total_chunks) {
            throw new InvalidArgumentException("Index de morceau invalide.");
        }

        $extension = strtolower($chunk->getClientOriginalExtension());
        $blockedExtensions = config('resumable_uploads.blocked_extensions', []);
        if (in_array($extension, $blockedExtensions)) {
            throw new InvalidArgumentException("Type de fichier non autorisé.");
        }

        if ($checksum && md5_file($chunk->getRealPath()) !== $checksum) {
            throw new InvalidArgumentException("Somme de contrôle du morceau invalide.");
        }

        $chunkName = sprintf('%06d', $chunkIndex) . '.part';
        $disk = Storage::disk($session->disk);
        $chunkPath = "{$session->temporary_path}/{$chunkName}";

        if ($disk->exists($chunkPath)) {
            // Idempotent: chunk already exists
            return [
                'received_chunks' => $session->received_chunks,
                'total_chunks' => $session->total_chunks,
                'is_complete' => $session->received_chunks === $session->total_chunks,
            ];
        }

        $chunk->storeAs($session->temporary_path, $chunkName, $session->disk);
        
        $session->received_chunks = count($session->received_chunk_indexes);
        $session->uploaded_bytes += $chunk->getSize();
        $session->last_activity_at = now();
        $session->status = 'uploading';
        $session->save();

        return [
            'received_chunks' => $session->received_chunks,
            'total_chunks' => $session->total_chunks,
            'is_complete' => $session->received_chunks === $session->total_chunks,
        ];
    }

    public function getStatus(UploadSession $session): array
    {
        return [
            'uuid' => $session->uuid,
            'status' => $session->status,
            'total_size' => $session->total_size,
            'uploaded_bytes' => $session->uploaded_bytes,
            'total_chunks' => $session->total_chunks,
            'received_chunks' => $session->received_chunk_indexes,
            'missing_chunks' => $session->missing_chunks,
            'progress' => $session->progress,
        ];
    }

    public function complete(UploadSession $session, string $originalFilename, int $declaredSize, ?string $globalChecksum = null): array
    {
        if ($session->isCompleted()) {
            return ['file' => $session->file, 'replaced_files' => []]; // Idempotence
        }

        $missing = $session->missing_chunks;
        if (!empty($missing)) {
            throw new InvalidArgumentException("Il manque des morceaux pour finaliser l'assemblage.");
        }

        $session->status = 'assembling';
        $session->save();

        $disk = Storage::disk($session->disk);
        $finalFilename = Str::random(40) . ($session->extension ? '.' . $session->extension : '');
        $finalRelPath = "files/{$session->user_id}/" . date('Y/m') . "/{$finalFilename}";
        $finalAbsPath = $disk->path($finalRelPath);
        
        $disk->makeDirectory(dirname($finalRelPath));

        $out = @fopen($finalAbsPath, 'wb');
        if (!$out) {
            throw new Exception("Impossible de créer le fichier final.");
        }

        $assembledSize = 0;
        try {
            for ($i = 0; $i < $session->total_chunks; $i++) {
                $chunkName = sprintf('%06d', $i) . '.part';
                $chunkAbsPath = $disk->path("{$session->temporary_path}/{$chunkName}");
                
                $in = @fopen($chunkAbsPath, 'rb');
                if (!$in) {
                    throw new Exception("Impossible de lire le morceau index {$i}.");
                }
                
                $copied = stream_copy_to_stream($in, $out);
                fclose($in);
                
                if ($copied === false) {
                    throw new Exception("Erreur lors de l'assemblage du morceau index {$i}.");
                }
                $assembledSize += $copied;
            }
            fclose($out);
        } catch (Exception $e) {
            if (is_resource($out)) fclose($out);
            @unlink($finalAbsPath);
            throw $e;
        }

        if ($assembledSize !== $declaredSize || $assembledSize !== $session->total_size) {
            @unlink($finalAbsPath);
            throw new Exception("Taille du fichier assemblé incorrecte.");
        }

        if ($globalChecksum && md5_file($finalAbsPath) !== $globalChecksum) {
            @unlink($finalAbsPath);
            throw new Exception("Somme de contrôle du fichier complet invalide.");
        }

        $replacedFiles = [];
        $existing = File::with('user')->where('name', $session->original_name)->where('folder_id', $session->folder_id)->first();
        if ($existing) {
            $folderName = $session->folder_id ? Folder::find($session->folder_id)->name : 'la racine';
            $by = $existing->user_id === $session->user_id ? 'vous' : $existing->user->name;
            $replacedFiles[] = ['name' => $session->original_name, 'folder' => $folderName, 'by' => $by];

            if (Storage::disk('private')->exists($existing->path)) {
                Storage::disk('private')->delete($existing->path);
            } elseif (Storage::disk('public')->exists($existing->path)) {
                Storage::disk('public')->delete($existing->path);
            }
            $existing->permissions()->delete();
            $existing->delete();
        }

        $file = DB::transaction(function () use ($session, $finalRelPath, $assembledSize) {
            $file = File::create([
                'name' => $session->original_name,
                'original_name' => $session->original_name,
                'path' => $finalRelPath,
                'extension' => $session->extension,
                'size' => $assembledSize,
                'folder_id' => $session->folder_id,
                'user_id' => $session->user_id,
            ]);

            AuditService::log('file_upload', 'File', $file->id, "A transféré le fichier : {$file->name}");

            $session->status = 'completed';
            $session->final_path = $finalRelPath;
            $session->file_id = $file->id;
            $session->completed_at = now();
            $session->save();

            return $file;
        });

        $this->cleanupChunks($session);

        return ['file' => $file, 'replaced_files' => $replacedFiles];
    }

    public function cancel(UploadSession $session): void
    {
        $session->status = 'cancelled';
        $session->save();
        $this->cleanupChunks($session);
    }

    public function cleanupExpiredUploads(): int
    {
        $sessions = UploadSession::expired()->get();
        $count = 0;

        foreach ($sessions as $session) {
            $session->status = 'expired';
            $session->save();
            $this->cleanupChunks($session);
            $count++;
        }

        return $count;
    }

    private function cleanupChunks(UploadSession $session): void
    {
        $disk = Storage::disk($session->disk);
        if ($disk->exists($session->temporary_path)) {
            $disk->deleteDirectory($session->temporary_path);
        }
        
        // Nettoyer le dossier parent de l'uuid s'il est vide
        $parentDir = dirname($session->temporary_path);
        if ($disk->exists($parentDir) && empty($disk->allFiles($parentDir))) {
            $disk->deleteDirectory($parentDir);
        }
    }

    private function validateFilename(string $filename): void
    {
        if (preg_match('/[\.\/\\\\]{2,}|[\x00-\x1F\x7F]/', $filename)) {
            throw new InvalidArgumentException("Nom de fichier invalide.");
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $blockedExtensions = config('resumable_uploads.blocked_extensions', []);
        if (in_array($extension, $blockedExtensions)) {
            throw new InvalidArgumentException("Extension de fichier non autorisée.");
        }
    }
}
