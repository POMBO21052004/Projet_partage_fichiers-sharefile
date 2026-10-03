<?php

namespace App\Http\Controllers\Uploads;

use App\Http\Controllers\Controller;
use App\Http\Requests\InitUploadRequest;
use App\Http\Requests\StoreChunkRequest;
use App\Models\UploadSession;
use App\Services\ResumableUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResumableUploadController extends Controller
{
    protected ResumableUploadService $service;

    public function __construct(ResumableUploadService $service)
    {
        $this->service = $service;
    }

    public function init(InitUploadRequest $request)
    {
        try {
            $user = $request->user();
            $role = $user->role ?? 'user';
            
            $session = $this->service->initialize($request->validated(), $user, $role);

            return response()->json([
                'success' => true,
                'upload_id' => $session->uuid,
                'chunk_size' => $session->chunk_size,
                'total_chunks' => $session->total_chunks,
                'received_chunks' => [],
                'status' => $session->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function chunk(StoreChunkRequest $request, string $uuid)
    {
        try {
            $session = UploadSession::where('uuid', $uuid)->first();
            
            if (!$session || !$session->isOwnedBy($request->user())) {
                return response()->json(['success' => false, 'message' => 'Session introuvable ou non autorisée.'], 403);
            }

            if (!$session->isActive()) {
                return response()->json([
                    'success' => false, 
                    'code' => 'UPLOAD_SESSION_INVALID', 
                    'message' => 'Session terminée ou annulée.'
                ], 409);
            }

            $result = $this->service->storeChunk(
                $session,
                $request->file('chunk'),
                $request->input('chunk_index'),
                $request->input('checksum')
            );

            return response()->json([
                'success' => true,
                'received' => $result['received_chunks'],
                'total' => $result['total_chunks'],
                'is_complete' => $result['is_complete'],
                'progress' => $session->progress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function status(Request $request, string $uuid)
    {
        $session = UploadSession::where('uuid', $uuid)->first();
        if (!$session || !$session->isOwnedBy($request->user())) {
            return response()->json(['success' => false, 'message' => 'Session introuvable.'], 404);
        }

        return response()->json([
            'success' => true,
        ] + $this->service->getStatus($session));
    }

    public function complete(Request $request, string $uuid)
    {
        $request->validate([
            'filename' => 'required|string',
            'filesize' => 'required|integer'
        ]);

        try {
            $session = UploadSession::where('uuid', $uuid)->first();
            if (!$session || !$session->isOwnedBy($request->user())) {
                return response()->json(['success' => false, 'message' => 'Session introuvable.'], 404);
            }

            if (in_array($session->status, ['cancelled', 'expired'])) {
                return response()->json(['success' => false, 'message' => 'Session annulée ou expirée.'], 409);
            }

            $file = $this->service->complete(
                $session,
                $request->input('filename'),
                $request->input('filesize'),
                $request->input('checksum')
            );

            return response()->json([
                'success' => true,
                'message' => 'Fichier transféré avec succès.',
                'file' => [
                    'id' => $file->id,
                    'name' => $file->name,
                    'size' => $file->size,
                    'extension' => $file->extension
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'assemblage du fichier : " . $e->getMessage(), ['uuid' => $uuid]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'assemblage du fichier.'
            ], 500);
        }
    }

    public function cancel(Request $request, string $uuid)
    {
        $session = UploadSession::where('uuid', $uuid)->first();
        if (!$session || !$session->isOwnedBy($request->user())) {
            return response()->json(['success' => false, 'message' => 'Session introuvable.'], 404);
        }

        $this->service->cancel($session);
        return response()->json(['success' => true, 'message' => 'Upload annulé.']);
    }
}
