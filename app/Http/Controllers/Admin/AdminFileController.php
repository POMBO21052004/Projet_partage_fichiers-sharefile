<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:3145728', // 3 Go max par fichier
            'folder_id' => 'nullable|exists:folders,id'
        ]);

        $uploadedFiles = $request->file('files');

        foreach ($uploadedFiles as $uploaded) {
            $file = File::create([
                'name' => $uploaded->getClientOriginalName(),
                'original_name' => $uploaded->getClientOriginalName(),
                'path' => $uploaded->store('uploads', 'public'),
                'extension' => $uploaded->getClientOriginalExtension(),
                'size' => $uploaded->getSize(),
                'folder_id' => $request->folder_id,
                'user_id' => auth()->id(),
            ]);

            \App\Services\AuditService::log(
                'file_upload',
                'File',
                $file->id,
                "A déposé le fichier : {$file->name}"
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => count($uploadedFiles) . ' fichier(s) transféré(s) avec succès.']);
        }

        return back()->with('success', count($uploadedFiles) . ' fichier(s) transféré(s) avec succès.');
    }

    public function show(File $file)
    {
        $file->load('user', 'sharedWith');
        return view('admin.files.show', compact('file'));
    }

    public function preview(File $file)
    {
        if (!Storage::disk('public')->exists($file->path)) {
            abort(404, "Fichier physique introuvable sur le disque (Path: " . $file->path . ")");
        }

        $path = Storage::disk('public')->path($file->path);
        $mimeType = mime_content_type($path);
        $supportedMimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'text/plain',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'video/mp4',
            'video/webm',
            'audio/mpeg',
            'audio/wav',
            'audio/ogg',
        ];

        if (in_array($mimeType, $supportedMimes)) {
            return response()->file($path);
        }

        return view('admin.files.preview_unsupported', compact('file', 'mimeType'));
    }

    public function download(File $file)
    {
        if (!Storage::disk('public')->exists($file->path)) {
            return back()->with('error', 'Le fichier physique est introuvable sur le disque.');
        }

        return Storage::disk('public')->download($file->path, $file->name);
    }

    public function permissions(File $file)
    {
        $file->load('sharedWith');
        $allUsers = User::where('role', 'user')->whereNotIn('id', $file->sharedWith->pluck('id'))->get();
        return view('admin.files.permissions', compact('file', 'allUsers'));
    }

    public function grantAccess(Request $request, File $file)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $file->sharedWith()->syncWithoutDetaching([$request->user_id]);

        $user = User::find($request->user_id);
        \App\Services\AuditService::log(
            'update_permission',
            'File',
            $file->id,
            "A accordé l'accès au fichier '{$file->name}' à l'utilisateur : {$user->name}"
        );

        return back()->with('success', 'Accès accordé avec succès.');
    }

    public function revokeAccess(File $file, User $user)
    {
        $file->sharedWith()->detach($user->id);

        \App\Services\AuditService::log(
            'update_permission',
            'File',
            $file->id,
            "A révoqué l'accès au fichier '{$file->name}' pour l'utilisateur : {$user->name}"
        );

        return back()->with('success', 'Accès révoqué avec succès.');
    }

    public function move(Request $request, File $file)
    {
        $request->validate(['folder_id' => 'nullable|exists:folders,id']);
        $file->update(['folder_id' => $request->folder_id]);

        $destName = $request->folder_id ? \App\Models\Folder::find($request->folder_id)->name : 'la racine';
        \App\Services\AuditService::log(
            'file_move',
            'File',
            $file->id,
            "A déplacé le fichier '{$file->name}' vers {$destName}"
        );

        return response()->json(['success' => true]);
    }

    public function destroy(File $file)
    {
        \App\Services\AuditService::log(
            'file_delete',
            'File',
            $file->id,
            "A supprimé définitivement le fichier : {$file->name}"
        );

        if (Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }
        $file->delete();
        return redirect()->route('admin.explorer', $file->folder_id)->with('success', 'Actif supprimé définitivement du Vault.');
    }
}
