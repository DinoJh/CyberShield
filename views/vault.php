<?php
// views/vault.php - Vista de bóveda encriptada con AES-256
$conn = getDBConnection();
$userId = $_SESSION['user_id'];

// Verificar si tiene contraseña de bóveda
$stmt = $conn->prepare("SELECT has_vault_password FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$hasVaultPassword = $user['has_vault_password'] ?? false;
$vaultUnlocked = $_SESSION['vault_unlocked'] ?? false;
?>

<!-- Modal de recomendación para crear contraseña de bóveda -->
<?php if (!$hasVaultPassword): ?>
<div id="vaultPasswordRecommendation" style="display: flex; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.95); z-index: 9999; justify-content: center; align-items: center;">
    <div class="terminal-box" style="max-width: 600px; width: 90%;">
        <div class="terminal-header">
            <div class="terminal-dot dot-red"></div>
            <div class="terminal-dot dot-yellow"></div>
            <div class="terminal-dot dot-green"></div>
            <div class="terminal-title">CONFIGURACIÓN DE SEGURIDAD RECOMENDADA</div>
        </div>
        <div style="padding: 30px;">
            <div style="text-align: center; margin-bottom: 25px;">
                <div style="font-size: 4em; margin-bottom: 15px;">🔐</div>
                <h3 style="color: #ffff00; text-shadow: 0 0 10px #ffff00; margin-bottom: 10px;">
                    ⚠️ SEGURIDAD ADICIONAL RECOMENDADA
                </h3>
            </div>
            
            <div style="background: rgba(0, 255, 255, 0.1); border: 1px solid #00ffff; padding: 20px; border-radius: 3px; margin-bottom: 20px;">
                <p style="color: #00ffff; line-height: 1.8; text-align: center;">
                    <strong>¿Por qué crear una contraseña de bóveda?</strong><br><br>
                    • <strong>Doble capa de protección:</strong> Incluso si alguien accede a tu cuenta, no podrá ver tus notas<br>
                    • <strong>Seguridad mejorada:</strong> Tus datos más sensibles estarán doblemente encriptados<br>
                    • <strong>Control total:</strong> Solo tú tendrás acceso a tu bóveda<br><br>
                    <span style="color: #00ff41;">Recomendado para máxima seguridad ✓</span>
                </p>
            </div>
            
            <form id="quickSetVaultPassword" onsubmit="quickSetVaultPassword(event)">
                <div class="input-group">
                    <div class="terminal-prompt">NUEVA CONTRASEÑA DE BÓVEDA (mín. 8 caracteres):</div>
                    <input type="password" id="quickVaultPassword" name="vault_password" placeholder="********" required autofocus>
                </div>
                <div class="input-group">
                    <div class="terminal-prompt">CONFIRMAR CONTRASEÑA:</div>
                    <input type="password" name="confirm_password" placeholder="********" required>
                </div>
                <button type="submit" class="btn" style="margin-bottom: 10px;">[ CREAR CONTRASEÑA DE BÓVEDA ]</button>
                <button type="button" class="btn" onclick="skipVaultPassword()" style="background: rgba(255, 255, 0, 0.1); border-color: #ffff00; color: #ffff00;">
                    [ OMITIR POR AHORA ]
                </button>
            </form>
            
            <p style="color: rgba(255, 255, 0, 0.7); font-size: 0.8em; text-align: center; margin-top: 15px;">
                Puedes configurar esto más tarde desde tu perfil
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal para desbloquear bóveda -->
<?php if ($hasVaultPassword && !$vaultUnlocked): ?>
<div id="unlockVaultModal" style="display: flex; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.95); z-index: 9999; justify-content: center; align-items: center;">
    <div class="terminal-box" style="max-width: 500px; width: 90%; position: relative;">
        <!-- Botón X para cerrar -->
        <button onclick="closeUnlockModal()" class="close-modal-btn" title="Cerrar">
            ✕
        </button>
        
        <div class="terminal-header">
            <div class="terminal-dot dot-red"></div>
            <div class="terminal-dot dot-yellow"></div>
            <div class="terminal-dot dot-green"></div>
            <div class="terminal-title">BÓVEDA BLOQUEADA</div>
        </div>
        <div style="padding: 30px;">
            <div style="text-align: center; margin-bottom: 25px;">
                <div style="font-size: 4em; margin-bottom: 15px;">🔒</div>
                <h3 style="color: #00ffff; text-shadow: 0 0 10px #00ffff; margin-bottom: 10px;">
                    ACCESO RESTRINGIDO
                </h3>
            </div>
            
            <div style="background: rgba(255, 255, 0, 0.1); border: 1px solid #ffff00; padding: 15px; border-radius: 3px; margin-bottom: 20px; text-align: center;">
                <p style="color: #ffff00;">
                    ⚠️ Esta bóveda está protegida con una contraseña adicional.<br>
                    Ingresa tu contraseña de bóveda para continuar.
                </p>
            </div>
            
            <form id="unlockVaultForm" onsubmit="unlockVault(event)">
                <div class="input-group">
                    <div class="terminal-prompt">CONTRASEÑA DE BÓVEDA:</div>
                    <input type="password" name="vault_password" placeholder="********" required autofocus>
                </div>
                <button type="submit" class="btn">[ DESBLOQUEAR BÓVEDA ]</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

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
    <h3>▓▒░ SUBIR DOCUMENTO ENCRIPTADO ░▒▓</h3>
    <form id="documentForm" onsubmit="uploadDocument(event)" enctype="multipart/form-data">
        <div class="input-group">
            <div class="terminal-prompt">TÍTULO DEL DOCUMENTO:</div>
            <input type="text" id="documentTitle" name="title" placeholder="documento_clasificado">
        </div>
        <div class="input-group">
            <div class="terminal-prompt">SELECCIONAR DOCUMENTO (máx. 10MB):</div>
            <input type="file" id="documentFile" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required style="padding: 10px; color: #00ff41;">
            <p style="color: rgba(0, 255, 255, 0.6); font-size: 0.8em; margin-top: 8px;">
                📄 Formatos soportados: PDF, Word (.doc, .docx), Excel (.xls, .xlsx), PowerPoint (.ppt, .pptx), TXT
            </p>
        </div>
        <button type="submit" class="btn">[ ENCRIPTAR Y SUBIR DOCUMENTO ]</button>
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
            📁 <strong style="color: #00ff41;">Tipos de archivo soportados:</strong><br>
            • Notas de texto<br>
            • Imágenes: JPEG, PNG, GIF, WEBP (máx. 5MB)<br>
            • Documentos: PDF, Word, Excel, PowerPoint, TXT (máx. 10MB)<br><br>
            ⚠️ <strong>IMPORTANTE:</strong> Sin tu clave maestra, los datos son irrecuperables.<br>
            ✓ Todos los archivos se encriptan con AES-256 antes de almacenarse
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
// Cerrar modal de recomendación
function closeRecommendationModal() {
    document.getElementById('vaultPasswordRecommendation').style.display = 'none';
}

// Cerrar modal de desbloqueo
function closeUnlockModal() {
    if (confirm('⚠️ Si cierras este modal, no podrás acceder a tu bóveda.\n\n¿Deseas salir de la bóveda?')) {
        window.location.href = 'index.php';
    }
}

// Mostrar formulario de contraseña de bóveda
function showVaultPasswordForm() {
    document.getElementById('vaultPasswordButtons').style.display = 'none';
    document.getElementById('quickSetVaultPassword').style.display = 'block';
    document.getElementById('quickVaultPassword').focus();
}

// Ocultar formulario de contraseña de bóveda
function hideVaultPasswordForm() {
    document.getElementById('vaultPasswordButtons').style.display = 'block';
    document.getElementById('quickSetVaultPassword').style.display = 'none';
    document.getElementById('quickSetVaultPassword').reset();
}

// Configuración rápida de contraseña de bóveda
async function quickSetVaultPassword(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'set_vault_password');
    
    try {
        const response = await fetch('api/profile.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            document.getElementById('vaultPasswordRecommendation').style.display = 'none';
            loadNotes();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al establecer contraseña', 'error');
        console.error(error);
    }
}

// Omitir configuración de contraseña
function skipVaultPassword() {
    if (confirm('¿Estás seguro? Puedes configurar la contraseña más tarde desde tu Perfil.')) {
        document.getElementById('vaultPasswordRecommendation').style.display = 'none';
        // Usar contraseña de cuenta como temporal
        loadNotes();
    }
}

// Desbloquear bóveda
async function unlockVault(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'unlock_vault');
    
    try {
        const response = await fetch('api/profile.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            document.getElementById('unlockVaultModal').style.display = 'none';
            loadNotes();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al desbloquear', 'error');
        console.error(error);
    }
}

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

// Subir documento encriptado
async function uploadDocument(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'upload_document');
    
    try {
        const response = await fetch('api/vault.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            document.getElementById('documentForm').reset();
            loadNotes();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al subir el documento', 'error');
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
            // Si el error es por bóveda bloqueada, no mostrar notificación
            if (!data.message.includes('ACCESO DENEGADO')) {
                showNotification(data.message, 'error');
            }
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
        let icon, typeLabel, actionButton;
        
        switch(note.content_type) {
            case 'image':
                icon = '🖼️';
                typeLabel = 'IMAGEN';
                actionButton = `<button class="btn-small" onclick="viewImage(${note.id})">[ VER IMAGEN ]</button>`;
                break;
            case 'document':
                // Iconos específicos por tipo de documento
                const docIcons = {
                    'pdf': '📕',
                    'doc': '📘',
                    'docx': '📘',
                    'xls': '📗',
                    'xlsx': '📗',
                    'ppt': '📙',
                    'pptx': '📙',
                    'txt': '📄'
                };
                icon = docIcons[note.file_extension] || '📄';
                typeLabel = 'DOCUMENTO .' + (note.file_extension || '').toUpperCase();
                actionButton = `<button class="btn-small" onclick="downloadDocument(${note.id})">[ DESCARGAR ]</button>`;
                break;
            default:
                icon = '📄';
                typeLabel = 'TEXTO';
                actionButton = `<button class="btn-small" onclick="viewNote(${note.id})">[ DESENCRIPTAR Y VER ]</button>`;
        }
        
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
                    ${actionButton}
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

// Descargar documento
function downloadDocument(noteId) {
    window.location.href = 'api/vault.php?action=download_document&note_id=' + noteId;
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
    // Solo cargar notas si no hay modal bloqueando
    const unlockModal = document.getElementById('unlockVaultModal');
    const recommendModal = document.getElementById('vaultPasswordRecommendation');
    
    if (!unlockModal && !recommendModal) {
        loadNotes();
    }
});
</script>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

/* Estilo para botón X de cerrar */
.close-modal-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 0, 0, 0.2);
    border: 1px solid #ff0000;
    color: #ff0000;
    width: 30px;
    height: 30px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 1.2em;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    font-weight: bold;
}

.close-modal-btn:hover {
    background: rgba(255, 0, 0, 0.4);
    box-shadow: 0 0 15px rgba(255, 0, 0, 0.6);
    transform: scale(1.1);
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