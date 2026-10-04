<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMyFilesController extends Controller
{
    public function index(Request $request, $folderId = null)
    {
        $user = Auth::user();
        $currentFolder = $folderId ? Folder::findOrFail($folderId) : null;

        // 1. Tous les folder_id des fichiers uploadés par cet admin
        $myFileFolderIds = File::where('user_id', $user->id)
            ->whereNotNull('folder_id')
            ->pluck('folder_id')
            ->unique()
            ->toArray();

        // 2. Remonter l'arbre des ancêtres pour chaque dossier trouvé
        $visibleFolderIds = $this->buildAncestorTree($myFileFolderIds);

        // 3. Sécurité : si on navigue dans un dossier, il doit être visible
        if ($currentFolder && !in_array($currentFolder->id, $visibleFolderIds)) {
            abort(403, "Vous n'avez aucun fichier dans ce dossier.");
        }

        // 4. Sous-dossiers du niveau courant qui sont dans les ancêtres visibles
        $folders = Folder::where('parent_id', $folderId)
            ->whereIn('id', $visibleFolderIds)
            ->withCount(['files' => fn($q) => $q->where('user_id', $user->id)])
            ->orderBy('name')
            ->get();

        // 5. Ses fichiers à lui dans ce dossier
        $files = File::where('user_id', $user->id)
            ->where('folder_id', $folderId)
            ->latest()
            ->get();

        // 6. Stats globales
        $totalFiles = File::where('user_id', $user->id)->count();
        $totalSizeBytes = File::where('user_id', $user->id)->sum('size');
        $totalFoldersCount = count(array_unique($myFileFolderIds));

        // 7. Breadcrumbs
        $breadcrumbs = [];
        if ($currentFolder) {
            $folder = $currentFolder;
            while ($folder) {
                array_unshift($breadcrumbs, $folder);
                $folder = $folder->parent_id ? Folder::find($folder->parent_id) : null;
            }
        }

        return view('admin.my-files.index', compact(
            'currentFolder', 'folders', 'files', 'breadcrumbs',
            'totalFiles', 'totalSizeBytes', 'totalFoldersCount'
        ));
    }

    /**
     * Remonte l'arbre des dossiers ancêtres à partir d'une liste de folder_id.
     * Retourne tous les IDs de dossiers visibles (les dossiers eux-mêmes + leurs ancêtres).
     */
    private function buildAncestorTree(array $folderIds): array
    {
        $allIds = [];
        $toProcess = array_unique($folderIds);

        while (!empty($toProcess)) {
            $folders = Folder::whereIn('id', $toProcess)->get(['id', 'parent_id']);
            $toProcess = [];

            foreach ($folders as $folder) {
                if (!in_array($folder->id, $allIds)) {
                    $allIds[] = $folder->id;
                }
                if ($folder->parent_id !== null && !in_array($folder->parent_id, $allIds)) {
                    $toProcess[] = $folder->parent_id;
                }
            }
            $toProcess = array_unique($toProcess);
        }

        return array_unique($allIds);
    }
}
