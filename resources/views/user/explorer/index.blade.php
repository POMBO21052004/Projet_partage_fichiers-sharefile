@extends('user.layouts.app')

@section('title', $currentFolder ? $currentFolder->name : 'Mes Fichiers')

@section('content')
<div class="space-y-6">

    {{-- Toolbar --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-surface-container-low dark:bg-slate-900/50 p-6 rounded-2xl ring-1 ring-outline-variant/10 dark:ring-slate-800">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-black text-on-surface dark:text-white tracking-tight uppercase">Mes Archives</h2>
            </div>
            {{-- Breadcrumbs --}}
            <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 flex-wrap">
                <a href="{{ route('user.explorer') }}" 
                   ondragover="event.preventDefault(); this.classList.add('text-primary', 'underline')" 
                   ondragleave="this.classList.remove('text-primary', 'underline')" 
                   ondrop="handleDrop(event, null, 'la racine')"
                   class="hover:text-primary dark:hover:text-blue-400 transition-colors flex items-center gap-1 shrink-0">
                    <span class="material-symbols-outlined text-sm">home</span> ACCUEIL
                </a>
                @foreach($breadcrumbs as $bc)
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <a href="{{ route('user.explorer', $bc->id) }}" 
                       ondragover="event.preventDefault(); this.classList.add('text-primary', 'underline')" 
                       ondragleave="this.classList.remove('text-primary', 'underline')" 
                       ondrop="handleDrop(event, {{ $bc->id }}, '{{ addslashes($bc->name) }}')"
                       class="hover:text-primary dark:hover:text-blue-400 transition-colors truncate max-w-[150px]" title="{{ $bc->name }}">{{ $bc->name }}</a>
                @endforeach
                @if($currentFolder)
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="text-primary dark:text-blue-400 truncate max-w-[150px]" title="{{ $currentFolder->name }}">{{ $currentFolder->name }}</span>
                @endif
            </nav>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('modal-folder').classList.remove('hidden')"
                class="flex items-center gap-2 px-5 py-2.5 bg-surface-container-lowest dark:bg-slate-800 ring-1 ring-outline-variant/20 dark:ring-slate-700 text-on-surface dark:text-slate-200 text-sm font-bold rounded-xl hover:shadow-md transition-all active:scale-95">
                <span class="material-symbols-outlined text-lg text-amber-500">create_new_folder</span> Nouveau dossier
            </button>
            <button onclick="document.getElementById('modal-upload').classList.remove('hidden')"
                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-primary-container text-white text-sm font-bold rounded-xl shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-95">
                <span class="material-symbols-outlined text-lg">cloud_upload</span> Uploader
            </button>
        </div>
    </div>

    {{-- Mes Dossiers --}}
    @if($folders->count() || $currentFolder)
    <div class="space-y-4">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary dark:text-blue-400">folder_open</span>
            <h3 class="text-[10px] font-black text-outline dark:text-slate-500 uppercase tracking-widest">Dossiers ({{ $folders->count() }})</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @if($currentFolder)
            <!-- Dossier Parent interactif & dropzone -->
            <div ondragover="event.preventDefault(); this.classList.add('ring-2', 'ring-primary', 'bg-primary/5')" 
                 ondragleave="this.classList.remove('ring-2', 'ring-primary', 'bg-primary/5')" 
                 ondrop="handleDrop(event, {{ $currentFolder->parent_id ?? 'null' }}, '{{ $currentFolder->parent ? addslashes($currentFolder->parent->name) : 'la racine' }}')"
                 class="group relative bg-surface-container-low/40 dark:bg-slate-900/40 p-5 rounded-2xl ring-1 ring-dashed ring-outline-variant/30 dark:ring-slate-800/50 hover:ring-primary/50 dark:hover:ring-blue-500/50 hover:shadow-xl transition-all duration-300">
                <a href="{{ $currentFolder->parent_id ? route('user.explorer', $currentFolder->parent_id) : route('user.explorer') }}" class="flex flex-col items-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-slate-400 dark:text-slate-500 group-hover:scale-110 transition-transform">keyboard_return</span>
                    <div class="text-center w-full min-w-0">
                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400 truncate">Dossier parent</p>
                        <p class="text-[10px] font-black text-outline dark:text-slate-500 truncate">RETOUR</p>
                    </div>
                </a>
            </div>
            @endif

            @foreach($folders as $folder)
            <div data-folder-id="{{ $folder->id }}" 
                 ondragover="event.preventDefault(); this.classList.add('ring-2', 'ring-primary', 'bg-primary/5')" 
                 ondragleave="this.classList.remove('ring-2', 'ring-primary', 'bg-primary/5')" 
                 ondrop="handleDrop(event, {{ $folder->id }}, '{{ $folder->name }}')"
                 class="group relative bg-surface-container-lowest dark:bg-slate-900 p-5 rounded-2xl ring-1 ring-outline-variant/10 dark:ring-slate-800 hover:ring-primary/30 dark:hover:ring-blue-500/30 hover:shadow-xl transition-all duration-300">
                <a href="{{ route('user.explorer', $folder->id) }}" class="flex flex-col items-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-amber-400 group-hover:scale-110 transition-transform" style="font-variation-settings: 'FILL' 1;">folder</span>
                    <div class="text-center w-full min-w-0">
                        <p class="text-sm font-bold text-on-surface dark:text-slate-200 truncate" title="{{ $folder->name }}">{{ $folder->name }}</p>
                        <p class="text-[10px] font-black text-outline dark:text-slate-500 truncate">{{ $folder->files_count }} ÉLÉMENTS</p>
                    </div>
                </a>
                
                @if($folder->user_id === Auth::id())
                <div class="absolute top-2 right-2 hidden group-hover:flex gap-1 z-10">
                    <button type="button" onclick="openRenameModal({{ $folder->id }}, '{{ addslashes($folder->name) }}')"
                        class="p-1.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg hover:bg-amber-500 hover:text-white transition-all" title="Renommer">
                        <span class="material-symbols-outlined text-sm">edit</span>
                    </button>
                    <button type="button" onclick="openDeleteModal('{{ route('user.folders.destroy', $folder->id) }}', true)"
                        class="p-1.5 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-lg hover:bg-rose-500 hover:text-white transition-all" title="Supprimer">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Mes Fichiers --}}
    @if($myFiles->count())
    <div class="space-y-4 pt-4">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary dark:text-blue-400">insert_drive_file</span>
            <h3 class="text-[10px] font-black text-outline dark:text-slate-500 uppercase tracking-widest">Fichiers ({{ $myFiles->count() }})</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($myFiles as $file)
            @php
                $ext = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
                $iconMap = [
                    'pdf'  => ['icon' => 'picture_as_pdf', 'color' => 'text-rose-500',    'bg' => 'bg-rose-50 dark:bg-rose-900/10'],
                    'doc'  => ['icon' => 'description',    'color' => 'text-blue-500',    'bg' => 'bg-blue-50 dark:bg-blue-900/10'],
                    'docx' => ['icon' => 'description',    'color' => 'text-blue-500',    'bg' => 'bg-blue-50 dark:bg-blue-900/10'],
                    'xls'  => ['icon' => 'table_chart',    'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/10'],
                    'xlsx' => ['icon' => 'table_chart',    'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/10'],
                    'ppt'  => ['icon' => 'slideshow',      'color' => 'text-orange-500',  'bg' => 'bg-orange-50 dark:bg-orange-900/10'],
                    'pptx' => ['icon' => 'slideshow',      'color' => 'text-orange-500',  'bg' => 'bg-orange-50 dark:bg-orange-900/10'],
                    'jpg'  => ['icon' => 'image',          'color' => 'text-purple-500',  'bg' => 'bg-purple-50 dark:bg-purple-900/10'],
                    'jpeg' => ['icon' => 'image',          'color' => 'text-purple-500',  'bg' => 'bg-purple-50 dark:bg-purple-900/10'],
                    'png'  => ['icon' => 'image',          'color' => 'text-purple-500',  'bg' => 'bg-purple-50 dark:bg-purple-900/10'],
                    'zip'  => ['icon' => 'folder_zip',     'color' => 'text-yellow-500',  'bg' => 'bg-yellow-50 dark:bg-yellow-900/10'],
                    'rar'  => ['icon' => 'folder_zip',     'color' => 'text-yellow-500',  'bg' => 'bg-yellow-50 dark:bg-yellow-900/10'],
                ];
                $fi = $iconMap[$ext] ?? ['icon' => 'draft', 'color' => 'text-slate-400', 'bg' => 'bg-slate-50 dark:bg-slate-900/20'];
            @endphp
            <div draggable="true" ondragstart="handleDragStart(event, {{ $file->id }}, '{{ $file->name }}')"
                class="group relative bg-surface-container-lowest dark:bg-slate-900 p-5 rounded-2xl ring-1 ring-outline-variant/10 dark:ring-slate-800 hover:ring-primary/30 dark:hover:ring-blue-500/30 hover:shadow-xl transition-all duration-300 cursor-grab active:cursor-grabbing">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-16 {{ $fi['bg'] }} rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl {{ $fi['color'] }}">{{ $fi['icon'] }}</span>
                    </div>
                    <div class="text-center w-full min-w-0">
                        <p class="text-sm font-bold text-on-surface dark:text-slate-200 truncate" title="{{ $file->name }}">{{ $file->name }}</p>
                        <p class="text-[10px] font-black text-outline dark:text-slate-500 uppercase">{{ strtoupper($file->extension) }} • {{ number_format($file->size / 1024, 1) }} Ko</p>
                    </div>
                </div>
                {{-- Hover actions --}}
                <div class="absolute inset-0 bg-white/95 dark:bg-slate-900/95 rounded-2xl hidden group-hover:flex items-center justify-center gap-2 transition-all">
                    <a href="{{ route('user.files.download', $file) }}"
                        class="p-2.5 bg-primary/10 dark:bg-blue-400/10 text-primary dark:text-blue-400 rounded-xl hover:bg-primary hover:text-white transition-all" title="Télécharger">
                        <span class="material-symbols-outlined text-base">download</span>
                    </a>
                    <button type="button" onclick="openDeleteModal('{{ route('user.files.destroy', $file->id) }}', false)" 
                        class="p-2.5 bg-rose-500/10 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all" title="Supprimer">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Fichiers Partagés --}}
    @if($sharedFiles->count())
    <div class="space-y-4 pt-4">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-emerald-500">folder_shared</span>
            <h3 class="text-[10px] font-black text-outline dark:text-slate-500 uppercase tracking-widest">Partagés avec moi ({{ $sharedFiles->count() }})</h3>
            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[9px] font-black rounded-full uppercase tracking-widest">Accès accordé</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($sharedFiles as $file)
            @php
                $ext = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
                $fi = $iconMap[$ext] ?? ['icon' => 'draft', 'color' => 'text-slate-400', 'bg' => 'bg-slate-50 dark:bg-slate-900/20'];
                $canDownload = $file->permissions->where('user_id', Auth::id())->first()?->can_download;
            @endphp
            <div class="group relative bg-emerald-50/20 dark:bg-emerald-950/5 p-5 rounded-2xl ring-1 ring-emerald-500/10 dark:ring-emerald-500/5 hover:ring-emerald-500/30 hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-16 {{ $fi['bg'] }} rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl {{ $fi['color'] }}">{{ $fi['icon'] }}</span>
                    </div>
                    <div class="text-center w-full min-w-0">
                        <p class="text-sm font-bold text-on-surface dark:text-slate-200 truncate" title="{{ $file->name }}">{{ $file->name }}</p>
                        <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase truncate">Partagé</p>
                    </div>
                </div>
                {{-- Hover actions --}}
                @if($canDownload)
                <div class="absolute inset-0 bg-emerald-50/95 dark:bg-slate-900/95 rounded-2xl hidden group-hover:flex items-center justify-center">
                    <a href="{{ route('user.files.download', $file) }}"
                        class="p-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors" title="Télécharger">
                        <span class="material-symbols-outlined text-base">download</span>
                    </a>
                </div>
                @else
                <div class="absolute top-2 right-2">
                    <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-base" title="Téléchargement non autorisé">lock</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Empty State --}}
    @if($folders->isEmpty() && $myFiles->isEmpty() && $sharedFiles->isEmpty())
    <div class="flex flex-col items-center justify-center py-32 space-y-4">
        <div class="w-24 h-24 bg-surface-container-low dark:bg-slate-900 rounded-full flex items-center justify-center">
            <span class="material-symbols-outlined text-6xl text-outline dark:text-slate-700">folder_open</span>
        </div>
        <div class="text-center">
            <h4 class="text-lg font-bold text-on-surface dark:text-white">Ce coffre est vide</h4>
            <p class="text-sm text-outline dark:text-slate-500">Commencez par créer un dossier ou par uploader un fichier.</p>
        </div>
    </div>
    @endif
</div>

{{-- Modal : Nouveau Dossier --}}
<div id="modal-folder" class="hidden fixed inset-0 bg-on-surface/40 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest dark:bg-slate-900 rounded-3xl p-8 w-full max-w-md shadow-2xl ring-1 ring-outline-variant/10">
        <h3 class="text-xl font-bold text-on-surface dark:text-white mb-6">Nouveau dossier</h3>
        <form action="{{ route('user.folders.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $currentFolder?->id }}">
            <input type="text" name="name" placeholder="Nom du dossier" required autofocus
                class="w-full bg-surface-container-low dark:bg-slate-800 border-none ring-1 ring-outline-variant/20 dark:ring-slate-700 focus:ring-primary rounded-2xl py-4 px-5 text-on-surface dark:text-white font-bold outline-none transition-all placeholder:text-outline/50">
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modal-folder').classList.add('hidden')"
                    class="flex-1 py-4 text-sm font-black uppercase tracking-widest text-outline hover:bg-surface-container-low dark:hover:bg-slate-800 rounded-2xl transition-all">
                    Annuler
                </button>
                <button type="submit" class="flex-1 py-4 bg-primary dark:bg-blue-600 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-primary/20">
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal : Renommer Dossier --}}
<div id="modal-rename" class="hidden fixed inset-0 bg-on-surface/40 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest dark:bg-slate-900 rounded-3xl p-8 w-full max-w-md shadow-2xl ring-1 ring-outline-variant/10">
        <h3 class="text-xl font-bold text-on-surface dark:text-white mb-6">Renommer le dossier</h3>
        <form id="form-rename" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <input type="text" name="name" id="rename-name-input" placeholder="Nouveau nom" required
                class="w-full bg-surface-container-low dark:bg-slate-800 border-none ring-1 ring-outline-variant/20 dark:ring-slate-700 focus:ring-primary rounded-2xl py-4 px-5 text-on-surface dark:text-white font-bold outline-none transition-all placeholder:text-outline/50">
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modal-rename').classList.add('hidden')"
                    class="flex-1 py-4 text-sm font-black uppercase tracking-widest text-outline hover:bg-surface-container-low dark:hover:bg-slate-800 rounded-2xl transition-all">
                    Annuler
                </button>
                <button type="submit" class="flex-1 py-4 bg-primary dark:bg-blue-600 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-primary/20">
                    Renommer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal : Upload Fichier --}}
<div id="modal-upload" class="hidden fixed inset-0 bg-on-surface/40 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest dark:bg-slate-900 rounded-3xl p-8 w-full max-w-md shadow-2xl ring-1 ring-outline-variant/10">
        <h3 class="text-xl font-bold text-on-surface dark:text-white mb-6">Transférer des fichiers</h3>
        <form id="upload-form" action="{{ route('user.files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <input type="hidden" name="folder_id" id="upload-folder-id" value="{{ $currentFolder?->id }}">
            <label class="flex flex-col items-center gap-4 p-12 border-2 border-dashed border-outline-variant/30 dark:border-slate-700 hover:border-primary dark:hover:border-blue-500 rounded-3xl cursor-pointer transition-all group">
                <span class="material-symbols-outlined text-6xl text-outline group-hover:text-primary transition-colors">upload_file</span>
                <span id="upload-text" class="text-xs font-black uppercase tracking-widest text-outline text-center group-hover:text-on-surface dark:group-hover:text-white">Déposer ou cliquer (multi-sélection possible)</span>
                <input type="file" name="files[]" id="upload-file-input" class="hidden" required multiple onchange="updateUploadText(this)">
            </label>
            
            <ul id="upload-file-list" class="hidden space-y-1 max-h-32 overflow-y-auto text-xs text-outline dark:text-slate-400"></ul>
            
            <div id="upload-progress-container" class="hidden space-y-2">
                <div class="flex justify-between items-center text-xs font-bold text-outline">
                    <span id="upload-progress-label">Envoi en cours...</span>
                    <span id="upload-progress-text">0%</span>
                </div>
                <div class="w-full h-2 bg-surface-container-highest dark:bg-slate-800 rounded-full overflow-hidden">
                    <div id="upload-progress-bar" class="h-full bg-primary dark:bg-blue-500 w-0 transition-all duration-300"></div>
                </div>
                <div class="flex justify-between items-center text-[10px] text-outline mt-1">
                    <span id="upload-speed-text">-- Mo/s</span>
                    <span id="upload-eta-text">-- restant</span>
                </div>
            </div>

            <div class="flex gap-3" id="upload-actions">
                <button type="button" onclick="cancelUploadModal()"
                    class="flex-1 py-4 text-sm font-black uppercase tracking-widest text-outline hover:bg-surface-container-low dark:hover:bg-slate-800 rounded-2xl transition-all">
                    Annuler
                </button>
                <button type="submit" id="upload-submit-btn" class="flex-1 py-4 bg-primary dark:bg-blue-600 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-primary/20">
                    Démarrer
                </button>
            </div>
            
            <div class="hidden gap-3" id="upload-retry-actions">
                <button type="button" onclick="cancelUploadModal()"
                    class="flex-1 py-4 text-sm font-black uppercase tracking-widest text-outline hover:bg-surface-container-low dark:hover:bg-slate-800 rounded-2xl transition-all">
                    Fermer
                </button>
                <button type="button" onclick="resumeUpload()" id="upload-resume-btn" class="flex-1 py-4 bg-amber-500 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-amber-500/20">
                    Reprendre
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modaux existants: suppression, deplacement... (déjà avant dans le fichier, je ne remplace que la fin) --}}

<script>
let draggedFile = null;
let targetFolder = null;

function handleDragStart(e, fileId, fileName) {
    draggedFile = { id: fileId, name: fileName };
    e.dataTransfer.setData('text/plain', fileId);
    e.target.classList.add('opacity-30');
}

function handleDrop(e, folderId, folderName) {
    e.preventDefault();
    const el = e.currentTarget;
    el.classList.remove('ring-2', 'ring-primary', 'bg-primary/5');
    
    if (draggedFile) {
        targetFolder = { id: folderId, name: folderName };
        document.getElementById('move-file-name').textContent = draggedFile.name;
        document.getElementById('move-folder-name').textContent = targetFolder.name;
        document.getElementById('modal-move').classList.remove('hidden');
    }
}

function cancelMove() {
    document.getElementById('modal-move').classList.add('hidden');
    draggedFile = null;
    targetFolder = null;
    document.querySelectorAll('[draggable="true"]').forEach(el => el.classList.remove('opacity-30'));
}

async function confirmMove() {
    if (!draggedFile || !targetFolder) return;
    try {
        const response = await fetch(`/user/files/${draggedFile.id}/move`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ folder_id: targetFolder.id })
        });
        const result = await response.json();
        if (result.success) window.location.reload();
        else alert(result.message);
    } catch (error) { alert('Erreur réseau'); }
}

function openRenameModal(folderId, currentName) {
    const modal = document.getElementById('modal-rename');
    const form = document.getElementById('form-rename');
    const input = document.getElementById('rename-name-input');
    
    form.action = `/user/folders/${folderId}`;
    input.value = currentName;
    
    modal.classList.remove('hidden');
    input.focus();
}

function openDeleteModal(actionUrl, isFolder) {
    const modal = document.getElementById('modal-delete');
    const form = document.getElementById('form-delete');
    const title = document.getElementById('delete-modal-title');
    const text = document.getElementById('delete-modal-text');
    
    form.action = actionUrl;
    if (isFolder) {
        title.textContent = "Supprimer ce dossier ?";
        text.textContent = "Attention : Tout le contenu (fichiers et sous-dossiers) sera définitivement supprimé.";
    } else {
        title.textContent = "Supprimer ce fichier ?";
        text.textContent = "Cette action est irréversible.";
    }
    
    modal.classList.remove('hidden');
}

window.updateUploadText = function(input) {
    const list = document.getElementById('upload-file-list');
    const text = document.getElementById('upload-text');
    
    if (!input || !input.files || input.files.length === 0) {
        text.textContent = 'Déposer ou cliquer (multi-sélection possible)';
        list.classList.add('hidden');
        list.innerHTML = '';
        return;
    }
    
    text.textContent = input.files.length + ' fichier(s) sélectionné(s)';
    list.innerHTML = '';
    list.classList.remove('hidden');
    
    Array.from(input.files).forEach(file => {
        const li = document.createElement('li');
        li.className = 'flex items-center gap-2';
        li.innerHTML = `<span class="material-symbols-outlined text-sm">draft</span> ${file.name} <span class="ml-auto opacity-50">(${(file.size / 1024 / 1024).toFixed(2)} Mo)</span>`;
        list.appendChild(li);
    });
};

function cancelUploadModal() {
    document.getElementById('modal-upload').classList.add('hidden');
    document.getElementById('upload-form').reset();
    window.updateUploadText(document.getElementById('upload-file-input'));
    document.getElementById('upload-progress-container').classList.add('hidden');
    document.getElementById('upload-actions').classList.remove('hidden');
    document.getElementById('upload-retry-actions').classList.add('hidden');
}

let currentUploadContext = null;

const uploadForm = document.getElementById('upload-form');
if (uploadForm) {
    uploadForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('upload-file-input');
        if (!fileInput || !fileInput.files.length) return;
        
        const files = Array.from(fileInput.files);
        
        document.getElementById('upload-actions').classList.add('hidden');
        document.getElementById('upload-progress-container').classList.remove('hidden');
        
        for (let i = 0; i < files.length; i++) {
            await processFile(files[i]);
        }
        
        document.getElementById('upload-progress-label').textContent = "Terminé !";
        setTimeout(() => window.location.reload(), 1000);
    });
}

async function processFile(file) {
    const SMALL_FILE_THRESHOLD = 10 * 1024 * 1024;
    const folderInput = document.getElementById('upload-folder-id');
    const folderId = folderInput ? folderInput.value : null;
    
    if (file.size < SMALL_FILE_THRESHOLD) {
        await uploadClassic(file, folderId);
    } else {
        await uploadResumable(file, folderId);
    }
}

async function uploadClassic(file, folderId) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('files[]', file);
        if (folderId) formData.append('folder_id', folderId);
        formData.append('_token', '{{ csrf_token() }}');
        
        const xhr = new XMLHttpRequest();
        xhr.open('POST', document.getElementById('upload-form').action, true);
        
        xhr.upload.addEventListener('progress', e => updateProgress(e.loaded, e.total, file.name));
        
        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) resolve();
            else reject('Erreur classique');
        };
        xhr.onerror = () => reject('Erreur réseau');
        xhr.send(formData);
    });
}

async function uploadResumable(file, folderId) {
    currentUploadContext = { file, folderId, uploadId: null, chunkSize: 0, totalChunks: 0, receivedChunks: [] };
    
    updateProgress(0, file.size, file.name, "Initialisation...");
    
    try {
        const initRes = await fetch('/uploads/resumable/init', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
                filename: file.name,
                filesize: file.size,
                mime_type: file.type,
                folder_id: folderId || null
            })
        });
        
        if (!initRes.ok) throw new Error("Erreur init");
        const initData = await initRes.json();
        
        currentUploadContext.uploadId = initData.upload_id;
        currentUploadContext.chunkSize = initData.chunk_size;
        currentUploadContext.totalChunks = initData.total_chunks;
        
        await uploadChunks();
        
        updateProgress(file.size, file.size, file.name, "Assemblage en cours...");
        const completeRes = await fetch(`/uploads/resumable/${currentUploadContext.uploadId}/complete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
                filename: file.name,
                filesize: file.size
            })
        });
        
        if (!completeRes.ok) throw new Error("Erreur assemblage");
        
    } catch (e) {
        document.getElementById('upload-progress-label').textContent = "Échec !";
        document.getElementById('upload-progress-bar').classList.replace('bg-primary', 'bg-rose-500');
        document.getElementById('upload-retry-actions').classList.remove('hidden');
        throw e;
    }
}

async function uploadChunks() {
    const { file, uploadId, chunkSize, totalChunks } = currentUploadContext;
    let uploadedBytes = 0;
    let startTime = Date.now();
    
    for (let i = 0; i < totalChunks; i++) {
        const start = i * chunkSize;
        const end = Math.min(start + chunkSize, file.size);
        const chunk = file.slice(start, end);
        
        const fd = new FormData();
        fd.append('chunk', chunk);
        fd.append('chunk_index', i);
        fd.append('total_chunks', totalChunks);
        fd.append('total_size', file.size);
        fd.append('filename', file.name);
        fd.append('_token', '{{ csrf_token() }}');
        
        let attempts = 0;
        let success = false;
        
        while (attempts < 5 && !success) {
            try {
                const res = await fetch(`/uploads/resumable/${uploadId}/chunk`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: fd
                });
                if (!res.ok) throw new Error("Chunk error");
                
                uploadedBytes += (end - start);
                updateProgress(uploadedBytes, file.size, file.name, "Envoi...");
                
                const elapsed = (Date.now() - startTime) / 1000;
                if (elapsed > 0) {
                    const speed = uploadedBytes / elapsed;
                    document.getElementById('upload-speed-text').textContent = (speed / 1024 / 1024).toFixed(2) + " Mo/s";
                }
                
                success = true;
            } catch (err) {
                attempts++;
                if (attempts >= 5) throw err;
                updateProgress(uploadedBytes, file.size, file.name, `Nouvelle tentative (${attempts}/5)...`);
                await new Promise(r => setTimeout(r, Math.pow(2, attempts) * 1000));
            }
        }
    }
}

window.resumeUpload = async function() {
    if (!currentUploadContext) return;
    document.getElementById('upload-retry-actions').classList.add('hidden');
    document.getElementById('upload-progress-bar').classList.replace('bg-rose-500', 'bg-primary');
    
    try {
        const res = await fetch(`/uploads/resumable/${currentUploadContext.uploadId}/status`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) {
            await uploadResumable(currentUploadContext.file, currentUploadContext.folderId);
            return;
        }
        
        const status = await res.json();
        currentUploadContext.receivedChunks = status.received_chunks;
        
        await uploadChunks();
        
        updateProgress(currentUploadContext.file.size, currentUploadContext.file.size, currentUploadContext.file.name, "Assemblage en cours...");
        const completeRes = await fetch(`/uploads/resumable/${currentUploadContext.uploadId}/complete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
                filename: currentUploadContext.file.name,
                filesize: currentUploadContext.file.size
            })
        });
        if (!completeRes.ok) throw new Error("Erreur assemblage");
        
        document.getElementById('upload-progress-label').textContent = "Terminé !";
        setTimeout(() => window.location.reload(), 1000);
    } catch (e) {
        document.getElementById('upload-progress-label').textContent = "Échec !";
        document.getElementById('upload-progress-bar').classList.replace('bg-primary', 'bg-rose-500');
        document.getElementById('upload-retry-actions').classList.remove('hidden');
    }
};

function updateProgress(loaded, total, filename, label = "Envoi en cours...") {
    const percent = Math.round((loaded / total) * 100);
    document.getElementById('upload-progress-bar').style.width = percent + '%';
    document.getElementById('upload-progress-text').textContent = percent + '%';
    document.getElementById('upload-progress-label').textContent = label;
}
</script>
@endsection
