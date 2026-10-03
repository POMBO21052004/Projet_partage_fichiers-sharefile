<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UploadSession extends Model
{
    protected $fillable = [
        'uuid', 'user_id', 'folder_id', 'original_name', 'extension', 
        'mime_type', 'total_size', 'chunk_size', 'total_chunks', 
        'received_chunks', 'uploaded_bytes', 'status', 'disk', 
        'temporary_path', 'final_path', 'file_id', 'checksum', 
        'last_activity_at', 'completed_at'
    ];

    protected $casts = [
        'total_size' => 'integer',
        'received_chunks' => 'integer',
        'uploaded_bytes' => 'integer',
        'last_activity_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'uploading']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getProgressAttribute(): float
    {
        if ($this->total_chunks === 0) return 0.0;
        if ($this->isCompleted()) return 100.0;
        
        $progress = ($this->received_chunks / $this->total_chunks) * 100;
        return round($progress, 2);
    }

    public function getReceivedChunkIndexesAttribute(): array
    {
        $disk = Storage::disk($this->disk);
        if (!$disk->exists($this->temporary_path)) {
            return [];
        }

        $files = $disk->files($this->temporary_path);
        $indexes = [];
        
        foreach ($files as $file) {
            $basename = basename($file);
            if (preg_match('/^(\d+)\.part$/', $basename, $matches)) {
                $indexes[] = (int) $matches[1];
            }
        }
        
        sort($indexes);
        return $indexes;
    }

    public function getMissingChunksAttribute(): array
    {
        $received = $this->getReceivedChunkIndexesAttribute();
        $expected = range(0, $this->total_chunks - 1);
        return array_values(array_diff($expected, $received));
    }

    public function scopeExpired($query)
    {
        $expirationMinutes = config('resumable_uploads.expiration_minutes', 1440);
        
        return $query->whereIn('status', ['pending', 'uploading'])
            ->where('last_activity_at', '<', now()->subMinutes($expirationMinutes));
    }
}
