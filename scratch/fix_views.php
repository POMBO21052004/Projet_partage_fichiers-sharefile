<?php
$views = [
    'resources/views/admin/explorer/index.blade.php',
    'resources/views/user/explorer/index.blade.php'
];

foreach ($views as $view) {
    $content = file_get_contents($view);
    
    // Check if the script block exists, if not, append it before @endsection
    if (strpos($content, 'function updateUploadText(') === false) {
        $isUser = strpos($view, 'user') !== false;
        $routePrefix = $isUser ? 'user' : 'admin';
        
        $script = <<<SCRIPT

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
    document.getElementById('upload-form').reset();
    updateUploadText(document.getElementById('upload-file-input'));
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
        if (!fileInput.files.length) return;
        
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
    const folderId = document.getElementById('upload-folder-id').value;
    
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
                
                const elapsed = (Date.now() - startTime) / 1000;
                if (elapsed > 0) {
                    const speed = uploadedBytes / elapsed;
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
        const res = await fetch(`/uploads/resumable/\${currentUploadContext.uploadId}/status`, {
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
        
        $content = str_replace('@endsection', $script . "\n@endsection", $content);
        file_put_contents($view, $content);
        echo "Fixed $view\n";
    }
}
