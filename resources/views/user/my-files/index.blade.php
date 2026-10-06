@extends('user.layouts.app')

@section('title', 'Mes Fichiers')

@section('content')
@php
    function fmtBytesUser($bytes) {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' Go';
        if ($bytes >= 1048576)   return number_format($bytes / 1048576, 2) . ' Mo';
        if ($bytes >= 1024)      return number_format($bytes / 1024, 1) . ' Ko';
        return $bytes . ' o';
    }
@endphp

<div class="space-y-6">

    {{-- Toolbar & Navigation --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-surface-container-low dark:bg-slate-900/50 p-6 rounded-2xl ring-1 ring-outline-variant/10 dark:ring-slate-800">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-black text-on-surface dark:text-white tracking-tight uppercase">Mes Fichiers</h2>
                <span class="px-3 py-1 bg-primary text-white text-[9px] font-black uppercase tracking-[0.2em] rounded-full shadow-lg shadow-primary/20">Personnel</span>
            </div>
            <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 flex-wrap">
                <a href="{{ route('user.my-files') }}"
                   class="hover:text-primary dark:hover:text-blue-400 transition-colors flex items-center gap-1 shrink-0">
                    <span class="material-symbols-outlined text-sm">home</span> ACCUEIL
                </a>
                @foreach($breadcrumbs as $bc)
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    @if($loop->last)
                        <span class="text-primary dark:text-blue-400">{{ $bc->name }}</span>
                    @else
                        <a href="{{ route('user.my-files', $bc->id) }}"
                           class="hover:text-primary dark:hover:text-blue-400 transition-colors truncate max-w-[150px]" title="{{ $bc->name }}">{{ $bc->name }}</a>
                    @endif
                @endforeach
            </nav>
        </div>

        <button onclick="document.getElementById('modal-upload').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-primary-container text-white text-sm font-bold rounded-xl shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-95">
            <span class="material-symbols-outlined text-lg">cloud_upload</span> Uploader
        </button>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface-container-lowest dark:bg-slate-900 p-5 rounded-2xl ring-1 ring-outline-variant/10 dark:ring-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl text-primary">insert_drive_file</span>
            </div>
            <div>
                <p class="text-2xl font-black text-on-surface dark:text-white">{{ $totalFiles }}</p>
                <p class="text-[10px] font-black text-outline dark:text-slate-500 uppercase tracking-widest">Fichiers uploadés</p>
            </div>
        </div>
        <div class="bg-surface-container-lowest dark:bg-slate-900 p-5 rounded-2xl ring-1 ring-outline-variant/10 dark:ring-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl text-amber-500">folder</span>
            </div>
            <div>
                <p class="text-2xl font-black text-on-surface dark:text-white">{{ $totalFoldersCount }}</p>
                <p class="text-[10px] font-black text-outline dark:text-slate-500 uppercase tracking-widest">Dossiers concernés</p>
            </div>
        </div>
        <div class="bg-surface-container-lowest dark:bg-slate-900 p-5 rounded-2xl ring-1 ring-outline-variant/10 dark:ring-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl text-emerald-500">storage</span>
            </div>
            <div>
                <p class="text-2xl font-black text-on-surface dark:text-white">{{ fmtBytesUser($totalSizeBytes) }}</p>
                <p class="text-[10px] font-black text-outline dark:text-slate-500 uppercase tracking-widest">Stockage utilisé</p>
            </div>
        </div>
    </div>

    {{-- Grille des Dossiers --}}
    @if($folders->count() || $currentFolder)
    <div class="space-y-4">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-amber-400">folder_open</span>
            <h3 class="text-[10px] font-black text-outline dark:text-slate-500 uppercase tracking-widest">Dossiers ({{ $folders->count() }})</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @if($currentFolder)
            <div class="group relative bg-surface-container-low/40 dark:bg-slate-900/40 p-5 rounded-2xl ring-1 ring-dashed ring-outline-variant/30 dark:ring-slate-800/50 hover:ring-primary/50 hover:shadow-xl transition-all duration-300">
                <a href="{{ $currentFolder->parent_id ? route('user.my-files', $currentFolder->parent_id) : route('user.my-files') }}" class="flex flex-col items-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-slate-400 dark:text-slate-500 group-hover:scale-110 transition-transform">keyboard_return</span>
                    <div class="text-center w-full min-w-0">
                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400 truncate">Dossier parent</p>
                        <p class="text-[10px] font-black text-outline dark:text-slate-500 truncate">RETOUR</p>
                    </div>
                </a>
            </div>
            @endif

            @foreach($folders as $folder)
            <div class="group relative bg-surface-container-lowest dark:bg-slate-900 p-5 rounded-2xl ring-1 ring-outline-variant/10 dark:ring-slate-800 hover:ring-primary/30 hover:shadow-xl transition-all duration-300">
                <a href="{{ route('user.my-files', $folder->id) }}" class="flex flex-col items-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-amber-400 group-hover:scale-110 transition-transform" style="font-variation-settings: 'FILL' 1;">folder</span>
                    <div class="text-center w-full min-w-0">
                        <p class="text-sm font-bold text-on-surface dark:text-slate-200 truncate" title="{{ $folder->name }}">{{ $folder->name }}</p>
                        <p class="text-[10px] font-black text-outline dark:text-slate-500 truncate">{{ $folder->files_count }} FICHIER(S) À MOI</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Grille des Fichiers --}}
    @if($files->count())
    <div class="space-y-4 pt-4">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary dark:text-blue-400">insert_drive_file</span>
            <h3 class="text-[10px] font-black text-outline dark:text-slate-500 uppercase tracking-widest">Mes fichiers ici ({{ $files->count() }})</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($files as $file)
            @php
                $ext = strtolower($file->extension ?? pathinfo($file->name, PATHINFO_EXTENSION));
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
                    'mp4'  => ['icon' => 'smart_display',  'color' => 'text-cyan-500',    'bg' => 'bg-cyan-50 dark:bg-cyan-900/10'],
                ];
                $fi = $iconMap[$ext] ?? ['icon' => 'draft', 'color' => 'text-slate-400', 'bg' => 'bg-slate-50 dark:bg-slate-900/20'];
            @endphp
            <div class="group relative bg-surface-container-lowest dark:bg-slate-900 p-5 rounded-2xl ring-1 ring-outline-variant/10 dark:ring-slate-800 hover:ring-primary/30 hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-16 {{ $fi['bg'] }} rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl {{ $fi['color'] }}">{{ $fi['icon'] }}</span>
                    </div>
                    <div class="text-center w-full min-w-0">
                        <p class="text-sm font-bold text-on-surface dark:text-slate-200 truncate" title="{{ $file->name }}">{{ $file->name }}</p>
                        <p class="text-[10px] font-black text-outline dark:text-slate-500 uppercase">{{ strtoupper($file->extension) }} • {{ number_format($file->size / 1024 / 1024, 2) }} Mo</p>
                    </div>
                </div>
                {{-- Actions au survol --}}
                <div class="absolute inset-0 bg-white/95 dark:bg-slate-900/95 rounded-2xl hidden group-hover:flex items-center justify-center gap-2 transition-all">
                    <a href="{{ route('user.files.download', $file) }}"
                        class="p-2.5 bg-primary/10 dark:bg-blue-400/10 text-primary dark:text-blue-400 rounded-xl hover:bg-primary hover:text-white transition-all" title="Télécharger">
                        <span class="material-symbols-outlined text-base">download</span>
                    </a>
                    <button type="button" onclick="openDeleteModal('{{ route('user.files.destroy', $file->id) }}')"
                        class="p-2.5 bg-rose-500/10 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all" title="Supprimer">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- État Vide --}}
    @if($folders->isEmpty() && $files->isEmpty())
    <div class="flex flex-col items-center justify-center py-32 space-y-4">
        <div class="w-24 h-24 bg-surface-container-low dark:bg-slate-900 rounded-full flex items-center justify-center">
            <span class="material-symbols-outlined text-6xl text-outline dark:text-slate-700">cloud_off</span>
        </div>
        <div class="text-center">
            @if($currentFolder)
                <h4 class="text-lg font-bold text-on-surface dark:text-white">Aucun de vos fichiers ici</h4>
                <p class="text-sm text-outline dark:text-slate-500">Ce dossier ne contient pas de fichiers que vous avez uploadés.</p>
            @else
                <h4 class="text-lg font-bold text-on-surface dark:text-white">Vous n'avez encore rien uploadé</h4>
                <p class="text-sm text-outline dark:text-slate-500">Utilisez le bouton "Uploader" pour transférer votre premier fichier.</p>
            @endif
        </div>
    </div>
    @endif

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
                <button type="submit" class="flex-1 py-4 bg-primary dark:bg-blue-600 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-primary/20">
                    Démarrer
                </button>
            </div>

            <div class="hidden gap-3" id="upload-retry-actions">
                <button type="button" onclick="cancelUploadModal()"
                    class="flex-1 py-4 text-sm font-black uppercase tracking-widest text-outline hover:bg-surface-container-low dark:hover:bg-slate-800 rounded-2xl transition-all">
                    Fermer
                </button>
                <button type="button" onclick="resumeUpload()" class="flex-1 py-4 bg-amber-500 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-amber-500/20">
                    Reprendre
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal : Confirmer suppression --}}
<div id="modal-delete" class="hidden fixed inset-0 bg-on-surface/40 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest dark:bg-slate-900 rounded-3xl p-8 w-full max-w-md shadow-2xl ring-1 ring-outline-variant/10">
        <h3 class="text-xl font-bold text-rose-600 dark:text-rose-500 mb-4">Supprimer ce fichier ?</h3>
        <p class="text-sm text-outline dark:text-slate-400 mb-6">Cette action est irréversible.</p>
        <form id="form-delete" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('modal-delete').classList.add('hidden')"
                class="flex-1 py-4 text-sm font-black uppercase tracking-widest text-outline hover:bg-surface-container-low dark:hover:bg-slate-800 rounded-2xl transition-all">
                Annuler
            </button>
            <button type="submit" class="flex-1 py-4 bg-rose-600 hover:bg-rose-700 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-rose-600/20">
                Supprimer
            </button>
        </form>
    </div>
</div>

{{-- Modal : Fichiers remplacés --}}
<div id="modal-replaced" class="hidden fixed inset-0 bg-on-surface/40 backdrop-blur-sm z-[70] flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest dark:bg-slate-900 rounded-3xl p-8 w-full max-w-md shadow-2xl ring-1 ring-outline-variant/10">
        <h3 class="text-xl font-bold text-amber-500 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined">info</span> Fichiers remplacés
        </h3>
        <p class="text-sm text-outline dark:text-slate-400 mb-4">
            Les fichiers suivants existaient déjà et ont été mis à jour avec votre nouvelle version :
        </p>
        <ul id="replaced-files-list" class="space-y-2 mb-6 max-h-40 overflow-y-auto text-sm">
        </ul>
        <button type="button" onclick="window.location.reload()" class="w-full py-4 bg-primary text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-primary/20">
            Fermer et actualiser
        </button>
    </div>
</div>
<script>
function openDeleteModal(actionUrl) {
    document.getElementById('form-delete').action = actionUrl;
    document.getElementById('modal-delete').classList.remove('hidden');
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
        let allReplaced = [];
        for (let i = 0; i < files.length; i++) {
            const res = await processFile(files[i]);
            if (res && res.replaced_files && res.replaced_files.length > 0) {
                allReplaced = allReplaced.concat(res.replaced_files);
            }
        }
        if (allReplaced.length > 0) {
            showReplacementModal(allReplaced);
        } else {
            document.getElementById('upload-progress-label').textContent = "Terminé !";
            setTimeout(() => window.location.reload(), 1000);
        }
    });
}

async function processFile(file) {
    const SMALL = 10 * 1024 * 1024;
    const folderId = document.getElementById('upload-folder-id')?.value || null;
    if (file.size < SMALL) {
        return await uploadClassic(file, folderId);
    } else {
        return await uploadResumable(file, folderId);
    }
}

async function uploadClassic(file, folderId) {
    return new Promise((resolve, reject) => {
        const fd = new FormData();
        fd.append('files[]', file);
        if (folderId) fd.append('folder_id', folderId);
        fd.append('_token', '{{ csrf_token() }}');
        const xhr = new XMLHttpRequest();
        xhr.open('POST', document.getElementById('upload-form').action, true);
        xhr.upload.addEventListener('progress', e => updateProgress(e.loaded, e.total));
        xhr.onload = () => xhr.status >= 200 && xhr.status < 300 ? resolve(JSON.parse(xhr.responseText)) : reject();
        xhr.onerror = () => reject();
        xhr.send(fd);
    });
}

async function uploadResumable(file, folderId) {
    currentUploadContext = { file, folderId, uploadId: null, chunkSize: 0, totalChunks: 0 };
    updateProgress(0, file.size, "Initialisation...");
    try {
        const initRes = await fetch('/uploads/resumable/init', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ filename: file.name, filesize: file.size, mime_type: file.type, folder_id: folderId || null })
        });
        if (!initRes.ok) throw new Error("init");
        const d = await initRes.json();
        currentUploadContext.uploadId = d.upload_id;
        currentUploadContext.chunkSize = d.chunk_size;
        currentUploadContext.totalChunks = d.total_chunks;
        await uploadChunks();
        updateProgress(file.size, file.size, "Assemblage...");
        const cr = await fetch(`/uploads/resumable/${currentUploadContext.uploadId}/complete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ filename: file.name, filesize: file.size })
        });
        if (!cr.ok) throw new Error("complete");
        return await cr.json();
    } catch (e) {
        document.getElementById('upload-progress-label').textContent = "Échec !";
        document.getElementById('upload-progress-bar').classList.replace('bg-primary', 'bg-rose-500');
        document.getElementById('upload-retry-actions').classList.remove('hidden');
        throw e;
    }
}

async function uploadChunks() {
    const { file, uploadId, chunkSize, totalChunks } = currentUploadContext;
    let uploaded = 0;
    const t0 = Date.now();
    for (let i = 0; i < totalChunks; i++) {
        const start = i * chunkSize;
        const end = Math.min(start + chunkSize, file.size);
        const fd = new FormData();
        fd.append('chunk', file.slice(start, end));
        fd.append('chunk_index', i);
        fd.append('total_chunks', totalChunks);
        fd.append('total_size', file.size);
        fd.append('filename', file.name);
        fd.append('_token', '{{ csrf_token() }}');
        let tries = 0;
        while (tries < 5) {
            try {
                const r = await fetch(`/uploads/resumable/${uploadId}/chunk`, { method: 'POST', headers: { 'Accept': 'application/json' }, body: fd });
                if (!r.ok) throw new Error();
                uploaded += (end - start);
                updateProgress(uploaded, file.size, "Envoi...");
                const speed = uploaded / ((Date.now() - t0) / 1000);
                document.getElementById('upload-speed-text').textContent = (speed / 1024 / 1024).toFixed(2) + " Mo/s";
                break;
            } catch { tries++; if (tries >= 5) throw new Error(); await new Promise(r => setTimeout(r, Math.pow(2, tries) * 1000)); }
        }
    }
}

window.resumeUpload = async function() {
    if (!currentUploadContext) return;
    document.getElementById('upload-retry-actions').classList.add('hidden');
    document.getElementById('upload-progress-bar').classList.replace('bg-rose-500', 'bg-primary');
    try {
        await uploadChunks();
        updateProgress(currentUploadContext.file.size, currentUploadContext.file.size, "Assemblage...");
        const cr = await fetch(`/uploads/resumable/${currentUploadContext.uploadId}/complete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ filename: currentUploadContext.file.name, filesize: currentUploadContext.file.size })
        });
        if (!cr.ok) throw new Error();
        const res = await cr.json();
        if (res && res.replaced_files && res.replaced_files.length > 0) {
            showReplacementModal(res.replaced_files);
        } else {
            document.getElementById('upload-progress-label').textContent = "Terminé !";
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch {
        document.getElementById('upload-progress-label').textContent = "Échec !";
        document.getElementById('upload-progress-bar').classList.replace('bg-primary', 'bg-rose-500');
        document.getElementById('upload-retry-actions').classList.remove('hidden');
    }
};

function showReplacementModal(files) {
    document.getElementById('modal-upload').classList.add('hidden');
    const list = document.getElementById('replaced-files-list');
    list.innerHTML = files.map(f => `
        <li class="bg-surface-container-low dark:bg-slate-800 p-3 rounded-xl mb-2">
            <strong class="text-on-surface dark:text-white">${f.name}</strong><br>
            <span class="text-xs text-outline">Dossier: ${f.folder} | Ancien uploader: ${f.by}</span>
        </li>
    `).join('');
    document.getElementById('modal-replaced').classList.remove('hidden');
}

function updateProgress(loaded, total, label = "Envoi en cours...") {
    const pct = Math.round((loaded / total) * 100);
    document.getElementById('upload-progress-bar').style.width = pct + '%';
    document.getElementById('upload-progress-text').textContent = pct + '%';
    document.getElementById('upload-progress-label').textContent = label;
    document.getElementById('upload-eta-text').textContent = (loaded / 1024 / 1024).toFixed(2) + ' Mo / ' + (total / 1024 / 1024).toFixed(2) + ' Mo';
}
</script>
@endsection
