<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;

class UserFolderController extends Controller
{
    public function index($folderId = null)
    {
        $user = Auth::user();
        $currentFolder = $folderId ? Folder::findOrFail($folderId) : null;

        // Récupérer les IDs des dossiers contenant des fichiers partagés avec l'utilisateur
        $sharedFileFolderIds = $user->sharedFiles()->whereNotNull('folder_id')->pluck('folder_id')->unique()->toArray();
        $ancestorFolderIds = [];
        $checkFolderIds = $sharedFileFolderIds;

        while (!empty($checkFolderIds)) {
            $foldersInfo = Folder::whereIn('id', $checkFolderIds)->get();
            $checkFolderIds = [];
            foreach ($foldersInfo as $fInfo) {
                $ancestorFolderIds[] = $fInfo->id;
                if ($fInfo->parent_id !== null && !in_array($fInfo->parent_id, $ancestorFolderIds)) {
                    $checkFolderIds[] = $fInfo->parent_id;
                }
            }
            $checkFolderIds = array_unique($checkFolderIds);
        }
        $allowedFolderIds = array_unique($ancestorFolderIds);

        // Sécurité : Refuser l'accès si le dossier actuel n'appartient pas à l'utilisateur ET n'est pas dans les dossiers partagés autorisés
        if ($currentFolder && $currentFolder->user_id !== $user->id && !in_array($currentFolder->id, $allowedFolderIds)) {
            abort(403, "Vous n'avez pas l'autorisation d'accéder à ce dossier.");
        }

        // Dossiers à afficher (les siens OU ceux contenant/ancêtres de fichiers partagés)
        $folders = Folder::where('parent_id', $folderId)
            ->where(function($query) use ($user, $allowedFolderIds) {
                $query->where('user_id', $user->id)
                      ->orWhereIn('id', $allowedFolderIds);
            })
            ->withCount('files')
            ->orderBy('name')
            ->get();

        // Ses propres fichiers dans ce dossier
        $myFiles = File::where('folder_id', $folderId)
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Fichiers partagés avec lui dans ce dossier
        $sharedFiles = $user->sharedFiles()
            ->where('folder_id', $folderId)
            ->latest()
            ->get();

        // Breadcrumbs
        $breadcrumbs = [];
        if ($currentFolder) {
            $folder = $currentFolder;
            while ($folder) {
                array_unshift($breadcrumbs, $folder);
                $folder = $folder->parent_id ? Folder::find($folder->parent_id) : null;
            }
        }

        return view('user.explorer.index', compact('currentFolder', 'folders', 'myFiles', 'sharedFiles', 'breadcrumbs'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Folder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id ?? null,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Dossier créé avec succès.');
    }

    public function update(\Illuminate\Http\Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres dossiers.');
        }

        $request->validate(['name' => 'required|string|max:255']);

        $folder->update(['name' => $request->name]);

        return back()->with('success', 'Dossier renommé avec succès.');
    }

    public function destroy(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres dossiers.');
        }

        $this->deleteFolderContent($folder);

        return back()->with('success', 'Dossier et son contenu supprimés avec succès.');
    }

    private function deleteFolderContent(Folder $folder)
    {
        // Supprimer les sous-dossiers récursivement
        foreach ($folder->children as $child) {
            $this->deleteFolderContent($child);
        }

        // Supprimer les fichiers de ce dossier
        foreach ($folder->files as $file) {
            if (\Illuminate\Support\Facades\Storage::disk('private')->exists($file->path)) {
                \Illuminate\Support\Facades\Storage::disk('private')->delete($file->path);
            }
            $file->permissions()->delete();
            $file->delete();
        }

        // Supprimer le dossier lui-même
        $folder->delete();
    }
}
