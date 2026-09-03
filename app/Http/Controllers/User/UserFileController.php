<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserFileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:3145728', // 3 Go max par fichier
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $uploadedFiles = $request->file('files');
        
        foreach ($uploadedFiles as $uploaded) {
            $originalName = $uploaded->getClientOriginalName();
            $extension = $uploaded->getClientOriginalExtension();
            $size = $uploaded->getSize();

            // Store securely in private disk
            $path = $uploaded->store('files/' . Auth::id() . '/' . date('Y/m'), 'private');

            File::create([
                'name' => $originalName,
                'original_name' => $originalName,
                'path' => $path,
                'extension' => $extension,
                'size' => $size,
                'folder_id' => $request->folder_id ?? null,
                'user_id' => Auth::id(),
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => count($uploadedFiles) . ' fichier(s) uploadé(s) avec succès.']);
        }

        return back()->with('success', count($uploadedFiles) . ' fichier(s) uploadé(s) avec succès.');
    }

    public function download(File $file)
    {
        $user = Auth::user();

        // Owner can always download
        if ($file->user_id === $user->id) {
            return $this->serveFile($file);
        }

        // Check shared permission with download right
        $permission = $file->permissions()
            ->where('user_id', $user->id)
            ->where('can_download', true)
            ->first();

        if ($permission) {
            return $this->serveFile($file);
        }

        abort(403, 'Vous n\'avez pas l\'autorisation de télécharger ce fichier.');
    }

    private function serveFile(File $file)
    {
        // Vérifie d'abord sur le disque privé
        if (Storage::disk('private')->exists($file->path)) {
            return Storage::disk('private')->download($file->path, $file->original_name);
        }

        // Repli (Fallback) sur le disque public
        if (Storage::disk('public')->exists($file->path)) {
            return Storage::disk('public')->download($file->path, $file->original_name);
        }

        abort(404, 'Fichier physique introuvable sur les disques de stockage.');
    }

    public function destroy(File $file)
    {
        // Only the owner can delete
        if ($file->user_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres fichiers.');
        }

        // Delete physical file
        if (Storage::disk('private')->exists($file->path)) {
            Storage::disk('private')->delete($file->path);
        }

        // Delete permissions
        $file->permissions()->delete();

        // Delete record
        $file->delete();

        return back()->with('success', 'Fichier supprimé.');
    }

    public function move(Request $request, File $file)
    {
        if ($file->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 403);
        }

        $request->validate([
            'folder_id' => 'required|exists:folders,id',
        ]);

        $file->update([
            'folder_id' => $request->folder_id
        ]);

        return response()->json(['success' => true, 'message' => 'Fichier déplacé.']);
    }
}
