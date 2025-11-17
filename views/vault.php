<?php
// views/vault.php - Vista de bóveda encriptada
?>

<div class="card">
    <h3>▓▒░ CREAR ENTRADA ENCRIPTADA ░▒▓</h3>
    <div class="input-group">
        <div class="terminal-prompt">TÍTULO DE ENTRADA:</div>
        <input type="text" id="noteTitle" placeholder="datos_clasificados">
    </div>
    <div class="input-group">
        <div class="terminal-prompt">CONTENIDO DE ENTRADA:</div>
        <textarea id="noteContent" placeholder="Tus datos serán encriptados con algoritmos de grado militar..." style="min-height: 120px;"></textarea>
    </div>
    <button class="btn" onclick="saveNote()">[ ENCRIPTAR Y ALMACENAR ]</button>
</div>

<div class="card">
    <h3>▓▒░ BÓVEDA ENCRIPTADA ░▒▓</h3>
    <div id="notesList">
        <p style="color: rgba(0, 255, 65, 0.5); text-align: center; padding: 20px;">
            [ BÓVEDA VACÍA ] No se encontraron entradas encriptadas
        </p>
    </div>
</div>

<div class="card">
    <h3>▓▒░ INFORMACIÓN DE SEGURIDAD ░▒▓</h3>
    <div style="background: rgba(0, 255, 255, 0.1); border: 1px solid #00ffff; padding: 15px; border-radius: 3px;">
        <p style="color: #00ffff; line-height: 1.8; font-size: 0.85em;">
            <strong>[ SISTEMA DE ENCRIPTACIÓN ]</strong><br><br>
            > Todas las entradas son encriptadas usando XOR + Base64<br>
            > Los datos se almacenan localmente en tu navegador<br>
            > Tu clave maestra es necesaria para desencriptar<br>
            > Las entradas persisten entre sesiones<br>
            > Próximamente: Soporte para imágenes encriptadas
        </p>
    </div>
</div>

<script>
// Guardar nota encriptada
function saveNote() {
    const title = document.getElementById('noteTitle').value.trim();
    const content = document.getElementById('noteContent').value.trim();
    
    if (!title || !content) {
        showNotification('[ ERROR ] Todos los campos requeridos para entrada en bóveda', 'error');
        return;
    }
    
    // Obtener clave maestra de la sesión
    const masterKey = '<?php echo $_SESSION['master_key'] ?? 'cyber2024'; ?>';
    
    // Encriptar contenido
    const encryptedContent = simpleEncrypt(content, masterKey);
    
    // Obtener notas existentes
    let notes = JSON.parse(localStorage.getItem('cybershield_notes') || '[]');
    
    // Agregar nueva nota
    notes.push({
        id: Date.now(),
        title: title,
        content: encryptedContent,
        date: new Date().toLocaleString('es-PE')
    });
    
    // Guardar
    localStorage.setItem('cybershield_notes', JSON.stringify(notes));
    
    // Limpiar formulario
    document.getElementById('noteTitle').value = '';
    document.getElementById('noteContent').value = '';
    
    // Actualizar estadísticas
    const stats = JSON.parse(localStorage.getItem('cybershield_stats') || '{"notes":0,"passwords":0,"messages":0}');
    stats.notes = notes.length;
    localStorage.setItem('cybershield_stats', JSON.stringify(stats));
    
    // Mostrar notas
    displayNotes();
    showNotification('[ ÉXITO ] Entrada encriptada y almacenada en bóveda');
}

// Mostrar notas
function displayNotes() {
    const notesList = document.getElementById('notesList');
    const notes = JSON.parse(localStorage.getItem('cybershield_notes') || '[]');
    
    if (notes.length === 0) {
        notesList.innerHTML = '<p style="color: rgba(0, 255, 65, 0.5); text-align: center; padding: 20px;">[ BÓVEDA VACÍA ] No se encontraron entradas encriptadas</p>';
        return;
    }
    
    notesList.innerHTML = notes.map(note => `
        <div class="note-item">
            <h4>${note.title} <span class="encrypted-badge">[ ENCRIPTADO ]</span></h4>
            <p style="font-size: 0.8em; color: rgba(0, 255, 255, 0.5);">MARCA DE TIEMPO: ${note.date}</p>
            <div class="note-actions">
                <button class="btn-small" onclick="viewNote(${note.id})">[ VER ]</button>
                <button class="btn-small" onclick="deleteNote(${note.id})">[ ELIMINAR ]</button>
            </div>
        </div>
    `).join('');
}

// Ver nota
function viewNote(id) {
    const notes = JSON.parse(localStorage.getItem('cybershield_notes') || '[]');
    const note = notes.find(n => n.id === id);
    
    if (note) {
        const masterKey = '<?php echo $_SESSION['master_key'] ?? 'cyber2024'; ?>';
        const decrypted = simpleDecrypt(note.content, masterKey);
        
        alert(`[ ENTRADA DESENCRIPTADA ]\n\nTÍTULO: ${note.title}\n\nCONTENIDO:\n${decrypted}`);
    }
}

// Eliminar nota
function deleteNote(id) {
    if (confirm('[ ADVERTENCIA ] ¿Eliminar permanentemente esta entrada encriptada?')) {
        let notes = JSON.parse(localStorage.getItem('cybershield_notes') || '[]');
        notes = notes.filter(n => n.id !== id);
        localStorage.setItem('cybershield_notes', JSON.stringify(notes));
        
        // Actualizar estadísticas
        const stats = JSON.parse(localStorage.getItem('cybershield_stats') || '{"notes":0,"passwords":0,"messages":0}');
        stats.notes = notes.length;
        localStorage.setItem('cybershield_stats', JSON.stringify(stats));
        
        displayNotes();
        showNotification('[ ELIMINADO ] Entrada borrada de la bóveda');
    }
}

// Cargar notas al iniciar
document.addEventListener('DOMContentLoaded', function() {
    displayNotes();
});
</script>