<?php
// views/vault.php - Vista de bóveda encriptada con AES-256
?>

<div class="card">
    <h3>▓▒░ CREAR ENTRADA ENCRIPTADA ░▒▓</h3>
    <form id="noteForm" onsubmit="saveNote(event)">
        <div class="input-group">
            <div class="terminal-prompt">TÍTULO DE ENTRADA:</div>
            <input type="text" id="noteTitle" name="title" placeholder="datos_clasificados" required>
        </div>
        <div class="input-group">
            <div class="terminal-prompt">CONTENIDO DE ENTRADA:</div>
            <textarea id="noteContent" name="content" placeholder="Tus datos serán encriptados con AES-256-CBC..." style="min-height: 120px;" required></textarea>
        </div>
        <button type="submit" class="btn">[ ENCRIPTAR Y ALMACENAR CON AES-256 ]</button>
    </form>
</div>

<div class="card">
    <h3>▓▒░ SUBIR IMAGEN ENCRIPTADA ░▒▓</h3>
    <form id="imageForm" onsubmit="uploadImage(event)" enctype="multipart/form-data">
        <div class="input-group">
            <div class="terminal-prompt">TÍTULO DE IMAGEN:</div>
            <input type="text" id="imageTitle" name="title" placeholder="imagen_confidencial">
        </div>
        <div class="input-group">
            <div class="terminal-prompt">SELECCIONAR IMAGEN (máx. 5MB):</div>
            <input type="file" id="imageFile" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required style="padding: 10px; color: #00ff41;">
        </div>
        <button type="submit" class="btn">[ ENCRIPTAR Y SUBIR IMAGEN ]</button>
    </form>
</div>

<div class="card">
    <h3>▓▒░ BÓVEDA ENCRIPTADA ░▒▓</h3>
    <div id="notesList">
        <p style="color: rgba(0, 255, 65, 0.5); text-align: center; padding: 20px;">
            <span style="display: inline-block; animation: pulse 1s infinite;">[ CARGANDO ]</span> Obteniendo entradas encriptadas...
        </p>
    </div>
</div>

<div class="card">
    <h3>▓▒░ INFORMACIÓN DE SEGURIDAD ░▒▓</h3>
    <div style="background: rgba(0, 255, 255, 0.1); border: 1px solid #00ffff; padding: 15px; border-radius: 3px;">
        <p style="color: #00ffff; line-height: 1.8; font-size: 0.85em;">
            <strong>[ SISTEMA DE ENCRIPTACIÓN MILITAR ]</strong><br><br>
            > <strong style="color: #00ff41;">Algoritmo:</strong> AES-256-CBC (Advanced Encryption Standard)<br>
            > <strong style="color: #00ff41;">Derivación de Clave:</strong> PBKDF2 con 100,000 iteraciones<br>
            > <strong style="color: #00ff41;">Hash:</strong> SHA-256<br>
            > <strong style="color: #00ff41;">IV:</strong> Vector de inicialización único por entrada<br>
            > <strong style="color: #00ff41;">Salt:</strong> Salt único por usuario<br>
            > <strong style="color: #00ff41;">Almacenamiento:</strong> Base de datos MySQL encriptada<br><br>
            ⚠️ <strong>IMPORTANTE:</strong> Sin tu clave maestra, los datos son irrecuperables.<br>
            ✓ Soporta texto e imágenes encriptadas
        </p>
    </div>
</div>

<!-- Modal para ver nota -->
<div id="noteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 9999; justify-content: center; align-items: center;">
    <div class="terminal-box" style="max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div class="terminal-header">
            <div class="terminal-dot dot-red"></div>
            <div class="terminal-dot dot-yellow"></div>
            <div class="terminal-dot dot-green"></div>
            <div class="terminal-title">ENTRADA DESENCRIPTADA</div>
        </div>
        <div style="padding: 30px;">
            <h3 id="modalTitle" style="color: #00ffff; text-shadow: 0 0 10px #00ffff; margin-bottom: 20px;"></h3>
            <div id="modalContent" style="background: rgba(0, 0, 0, 0.5); padding: 20px; border: 1px solid #00ff41; border-radius: 3px; color: #00ff41; line-height: 1.8; white-space: pre-wrap; word-wrap: break-word;"></div>
            <p id="modalDate" style="color: rgba(0, 255, 65, 0.5); margin-top: 15px; font-size: 0.8em;"></p>
            <button class="btn" onclick="closeModal()" style="margin-top: 20px;">[ CERRAR ]</button>
        </div>
    </div>
</div>

<script>
// Guardar nota encriptada
async function saveNote(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'save');
    
    try {
        const response = await fetch('api/vault.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            document.getElementById('noteForm').reset();
            loadNotes();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al guardar la nota', 'error');
        console.error(error);
    }
}

// Subir imagen encriptada
async function uploadImage(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'upload_image');
    
    try {
        const response = await fetch('api/vault.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            document.getElementById('imageForm').reset();
            loadNotes();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al subir la imagen', 'error');
        console.error(error);
    }
}

// Cargar todas las notas
async function loadNotes() {
    try {
        const response = await fetch('api/vault.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            displayNotes(data.data.notes);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        document.getElementById('notesList').innerHTML = '<p style="color: rgba(255, 0, 0, 0.7); text-align: center; padding: 20px;">[ ERROR ] No se pudieron cargar las notas</p>';
        console.error(error);
    }
}

// Mostrar notas en la lista
function displayNotes(notes) {
    const notesList = document.getElementById('notesList');
    
    if (notes.length === 0) {
        notesList.innerHTML = '<p style="color: rgba(0, 255, 65, 0.5); text-align: center; padding: 20px;">[ BÓVEDA VACÍA ] No se encontraron entradas encriptadas</p>';
        return;
    }
    
    notesList.innerHTML = notes.map(note => {
        const icon = note.content_type === 'image' ? '🖼️' : '📄';
        const typeLabel = note.content_type === 'image' ? 'IMAGEN' : 'TEXTO';
        const date = new Date(note.created_at).toLocaleString('es-PE');
        
        return `
            <div class="note-item">
                <h4>
                    ${icon} ${note.title} 
                    <span class="encrypted-badge">[ ${typeLabel} ENCRIPTADO AES-256 ]</span>
                </h4>
                <p style="font-size: 0.8em; color: rgba(0, 255, 255, 0.5);">
                    MARCA DE TIEMPO: ${date}
                </p>
                <div class="note-actions">
                    ${note.content_type === 'text' ? 
                        `<button class="btn-small" onclick="viewNote(${note.id})">[ DESENCRIPTAR Y VER ]</button>` :
                        `<button class="btn-small" onclick="viewImage(${note.id})">[ DESCARGAR IMAGEN ]</button>`
                    }
                    <button class="btn-small" onclick="deleteNote(${note.id})">[ ELIMINAR ]</button>
                </div>
            </div>
        `;
    }).join('');
}

// Ver nota desencriptada
async function viewNote(noteId) {
    try {
        const formData = new FormData();
        formData.append('action', 'view');
        formData.append('note_id', noteId);
        
        const response = await fetch('api/vault.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            const note = data.data.note;
            document.getElementById('modalTitle').textContent = '🔓 ' + note.title;
            document.getElementById('modalContent').textContent = note.content;
            document.getElementById('modalDate').textContent = 'Creada: ' + new Date(note.created_at).toLocaleString('es-PE');
            document.getElementById('noteModal').style.display = 'flex';
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al desencriptar la nota', 'error');
        console.error(error);
    }
}

// Ver/descargar imagen
function viewImage(noteId) {
    window.open('api/vault.php?action=download_image&note_id=' + noteId, '_blank');
}

// Cerrar modal
function closeModal() {
    document.getElementById('noteModal').style.display = 'none';
}

// Eliminar nota
async function deleteNote(noteId) {
    if (!confirm('[ ADVERTENCIA ] ¿Eliminar permanentemente esta entrada encriptada?\n\nEsta acción no se puede deshacer.')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('note_id', noteId);
        
        const response = await fetch('api/vault.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            loadNotes();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al eliminar la nota', 'error');
        console.error(error);
    }
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Cargar notas al iniciar
document.addEventListener('DOMContentLoaded', function() {
    loadNotes();
});
</script>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

input[type="file"] {
    background: rgba(0, 0, 0, 0.5);
    border: 1px solid #00ff41;
    border-radius: 3px;
    font-family: 'Fira Code', monospace;
    font-size: 0.9em;
    cursor: pointer;
}

input[type="file"]:hover {
    border-color: #00ffff;
    box-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
}

input[type="file"]::file-selector-button {
    background: rgba(0, 255, 65, 0.2);
    border: 1px solid #00ff41;
    color: #00ff41;
    padding: 8px 15px;
    cursor: pointer;
    font-family: 'Fira Code', monospace;
    margin-right: 10px;
}

input[type="file"]::file-selector-button:hover {
    background: rgba(0, 255, 65, 0.3);
}
</style>