<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\File;
use App\Models\Folder;

class AdminFolderController extends Controller
{
    public function index(Request $request, $folderId = null)
    {
        $currentFolder = $folderId ? Folder::findOrFail($folderId) : null;
        $isMyFiles = $request->get('owner') === 'me';

        $foldersQuery = Folder::where('parent_id', $folderId)
            ->withCount('files')
            ->orderBy('name');

        $filesQuery = File::with('user')
            ->where('folder_id', $folderId)
            ->latest();

        if ($isMyFiles) {
            $foldersQuery->where('user_id', auth()->id());
            $filesQuery->where('user_id', auth()->id());
        }

        $folders = $foldersQuery->get();
        $files = $filesQuery->get();

        // Breadcrumbs
        $breadcrumbs = [];
        if ($currentFolder) {
            $folder = $currentFolder;
            while ($folder) {
                array_unshift($breadcrumbs, $folder);
                $folder = $folder->parent_id ? Folder::find($folder->parent_id) : null;
            }
        }

        return view('admin.explorer.index', compact('currentFolder', 'folders', 'files', 'breadcrumbs'));
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
        $request->validate(['name' => 'required|string|max:255']);

        $folder->update(['name' => $request->name]);

        return back()->with('success', 'Dossier renommé avec succès.');
    }

    public function destroy(Folder $folder)
    {
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
