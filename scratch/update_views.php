<?php
$views = [
    'resources/views/admin/explorer/index.blade.php',
    'resources/views/user/explorer/index.blade.php'
];

foreach ($views as $view) {
    $content = file_get_contents($view);
    
    // Replace modal-upload
    $modalStart = strpos($content, '<div id="modal-upload"');
    $scriptStart = strpos($content, '<script>');
    $end = strpos($content, '@endsection');
    
    if ($modalStart === false || $scriptStart === false || $end === false) {
        echo "Error in $view\n";
        continue;
    }

    $isUser = strpos($view, 'user') !== false;
    $routePrefix = $isUser ? 'user' : 'admin';

    $newModalAndScript = <<<HTML
<div id="modal-upload" class="hidden fixed inset-0 bg-on-surface/40 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest dark:bg-slate-900 rounded-3xl p-8 w-full max-w-md shadow-2xl ring-1 ring-outline-variant/10">
        <h3 class="text-xl font-bold text-on-surface dark:text-white mb-6">Transférer des fichiers</h3>
        <form id="upload-form" action="{{ route('{$routePrefix}.files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <input type="hidden" name="folder_id" id="upload-folder-id" value="{{ \$currentFolder?->id }}">
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
HTML;

    // We only want to replace from <div id="modal-upload"> up to just before @endsection (excluding it so we can re-add it)
    $part1 = substr($content, 0, $modalStart);
    $part2 = <<<SCRIPT
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
        const response = await fetch(`/{$routePrefix}/files/\${draggedFile.id}/move`, {
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
    
    form.action = `/{$routePrefix}/folders/\${folderId}`;
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

function updateUploadText(input) {
    const list = document.getElementById('upload-file-list');
    const text = document.getElementById('upload-text');
    const count = input.files.length;
    
    if (count === 0) {
        text.textContent = 'Déposer ou cliquer (multi-sélection possible)';
        list.classList.add('hidden');
        list.innerHTML = '';
        return;
    }
    
    text.textContent = count + ' fichier(s) sélectionné(s)';
    list.innerHTML = '';
    list.classList.remove('hidden');
    
    Array.from(input.files).forEach(file => {
        const li = document.createElement('li');
        li.className = 'flex items-center gap-2';
        li.innerHTML = `<span class="material-symbols-outlined text-sm">draft</span> \${file.name} <span class="ml-auto opacity-50">(\${(file.size / 1024 / 1024).toFixed(2)} Mo)</span>`;
        list.appendChild(li);
    });
}

function cancelUploadModal() {
    document.getElementById('modal-upload').classList.add('hidden');
    // reset form
    document.getElementById('upload-form').reset();
    updateUploadText(document.getElementById('upload-file-input'));
    document.getElementById('upload-progress-container').classList.add('hidden');
    document.getElementById('upload-actions').classList.remove('hidden');
    document.getElementById('upload-retry-actions').classList.add('hidden');
}

// === LOGIQUE UPLOAD RÉSUMABLE ===
let currentUploadContext = null;

const uploadForm = document.getElementById('upload-form');
if (uploadForm) {
    uploadForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('upload-file-input');
        if (!fileInput.files.length) return;
        
        // Pour cet exemple, on gère le 1er fichier pour le resumable (multi-fichier nécessiterait une file d'attente complexe)
        // Dans une V1 complète on bouclerait, ici on bloque à 1 pour simplifier la démo ou on uploade séquentiellement.
        const files = Array.from(fileInput.files);
        
        document.getElementById('upload-actions').classList.add('hidden');
        document.getElementById('upload-file-list').classList.add('hidden');
        document.getElementById('upload-progress-container').classList.remove('hidden');
        
        for (let i = 0; i < files.length; i++) {
            await processFile(files[i]);
        }
        
        document.getElementById('upload-progress-label').textContent = "Terminé !";
        setTimeout(() => window.location.reload(), 1000);
    });
}

async function processFile(file) {
    const SMALL_FILE_THRESHOLD = 10 * 1024 * 1024; // 10 Mo
    const folderId = document.getElementById('upload-folder-id').value;
    
    if (file.size < SMALL_FILE_THRESHOLD) {
        // Fallback classique
        await uploadClassic(file, folderId);
    } else {
        // Upload Résumable
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
    
    // 1. Init
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
        
        // 2. Chunks
        await uploadChunks();
        
        // 3. Complete
        updateProgress(file.size, file.size, file.name, "Assemblage en cours...");
        const completeRes = await fetch(`/uploads/resumable/\${currentUploadContext.uploadId}/complete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
                filename: file.name,
                filesize: file.size
            })
        });
        
        if (!completeRes.ok) throw new Error("Erreur assemblage");
        
    } catch (e) {
        console.error(e);
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
                const res = await fetch(`/uploads/resumable/\${uploadId}/chunk`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: fd
                });
                if (!res.ok) throw new Error("Chunk error");
                
                uploadedBytes += (end - start);
                updateProgress(uploadedBytes, file.size, file.name, "Envoi...");
                
                // Calcul vitesse
                const elapsed = (Date.now() - startTime) / 1000;
                if (elapsed > 0) {
                    const speed = uploadedBytes / elapsed; // bytes/sec
                    document.getElementById('upload-speed-text').textContent = (speed / 1024 / 1024).toFixed(2) + " Mo/s";
                }
                
                success = true;
            } catch (err) {
                attempts++;
                if (attempts >= 5) throw err;
                updateProgress(uploadedBytes, file.size, file.name, `Nouvelle tentative (\${attempts}/5)...`);
                await new Promise(r => setTimeout(r, Math.pow(2, attempts) * 1000));
            }
        }
    }
}

async function resumeUpload() {
    if (!currentUploadContext) return;
    document.getElementById('upload-retry-actions').classList.add('hidden');
    document.getElementById('upload-progress-bar').classList.replace('bg-rose-500', 'bg-primary');
    
    try {
        // Get status
        const res = await fetch(`/uploads/resumable/\${currentUploadContext.uploadId}/status`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) {
            // Re-init complet
            await uploadResumable(currentUploadContext.file, currentUploadContext.folderId);
            return;
        }
        
        const status = await res.json();
        currentUploadContext.receivedChunks = status.received_chunks;
        
        // Hack rapide: on va relancer l'upload des chunks manquants
        // Pour simplifier ce script de patch, on refait un uploadResumable (idempotent côté serveur)
        await uploadChunks();
        
        updateProgress(currentUploadContext.file.size, currentUploadContext.file.size, currentUploadContext.file.name, "Assemblage en cours...");
        const completeRes = await fetch(`/uploads/resumable/\${currentUploadContext.uploadId}/complete`, {
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
}

function updateProgress(loaded, total, filename, label = "Envoi en cours...") {
    const percent = Math.round((loaded / total) * 100);
    document.getElementById('upload-progress-bar').style.width = percent + '%';
    document.getElementById('upload-progress-text').textContent = percent + '%';
    document.getElementById('upload-progress-label').textContent = label;
}
</script>
SCRIPT;

    $finalContent = $part1 . $newModalAndScript . "\n@endsection\n";
    file_put_contents($view, $finalContent);
    echo "Updated $view\n";
}
