<?php
// views/messages.php - Vista de comunicación segura
?>

<div class="card">
    <h3>▓▒░ MÓDULO DE ENCRIPTACIÓN DE MENSAJES ░▒▓</h3>
    <div class="input-group">
        <div class="terminal-prompt">MENSAJE EN TEXTO PLANO:</div>
        <textarea id="messageToEncrypt" placeholder="Escribe tu mensaje secreto aquí..."></textarea>
    </div>
    <button class="btn" onclick="encryptMessage()">[ ENCRIPTAR MENSAJE ]</button>
    <div class="input-group" style="margin-top: 15px;">
        <div class="terminal-prompt">SALIDA ENCRIPTADA:</div>
        <textarea id="encryptedMessage" readonly style="background: rgba(0, 0, 0, 0.7);"></textarea>
    </div>
    <button class="btn-small" onclick="copyToClipboard('encryptedMessage')">[ COPIAR AL PORTAPAPELES ]</button>
</div>

<div class="card">
    <h3>▓▒░ MÓDULO DE DESENCRIPTACIÓN DE MENSAJES ░▒▓</h3>
    <div class="input-group">
        <div class="terminal-prompt">MENSAJE ENCRIPTADO:</div>
        <textarea id="messageToDecrypt" placeholder="Pega el mensaje encriptado aquí..."></textarea>
    </div>
    <button class="btn" onclick="decryptMessage()">[ DESENCRIPTAR MENSAJE ]</button>
    <div class="input-group" style="margin-top: 15px;">
        <div class="terminal-prompt">SALIDA DESENCRIPTADA:</div>
        <textarea id="decryptedMessage" readonly style="background: rgba(0, 0, 0, 0.7);"></textarea>
    </div>
</div>

<div class="card">
    <h3>▓▒░ COMUNICACIÓN ENTRE USUARIOS (PRÓXIMAMENTE) ░▒▓</h3>
    <div style="background: rgba(255, 255, 0, 0.1); border: 1px solid #ffff00; padding: 20px; border-radius: 3px; text-align: center;">
        <p style="color: #ffff00; font-size: 1.2em; margin-bottom: 15px;">
            [ EN DESARROLLO ]
        </p>
        <p style="color: rgba(255, 255, 0, 0.7); line-height: 1.8; font-size: 0.9em;">
            Próximamente podrás enviar mensajes encriptados a otros usuarios de CyberShield.<br>
            Esta función incluirá:<br><br>
            > Sistema de usuarios y contactos<br>
            > Mensajería encriptada de extremo a extremo<br>
            > Notificaciones en tiempo real<br>
            > Historial de conversaciones seguro<br>
            > Verificación de identidad
        </p>
    </div>
</div>

<div class="card">
    <h3>▓▒░ GENERADOR DE HASH SHA-256 ░▒▓</h3>
    <div class="input-group">
        <div class="terminal-prompt">CADENA DE ENTRADA:</div>
        <input type="text" id="hashInput" placeholder="datos_para_hash">
    </div>
    <button class="btn" onclick="generateHash()">[ GENERAR HASH ]</button>
    <div class="input-group" style="margin-top: 15px;">
        <div class="terminal-prompt">SALIDA SHA-256:</div>
        <input type="text" id="hashOutput" readonly style="background: rgba(0, 0, 0, 0.7);">
    </div>
</div>

<script>
// Encriptar mensaje
function encryptMessage() {
    const message = document.getElementById('messageToEncrypt').value.trim();
    
    if (!message) {
        showNotification('[ ERROR ] No se proporcionó mensaje para encriptar', 'error');
        return;
    }
    
    const masterKey = '<?php echo $_SESSION['master_key'] ?? 'cyber2024'; ?>';
    const encrypted = simpleEncrypt(message, masterKey);
    document.getElementById('encryptedMessage').value = encrypted;
    
    // Actualizar estadísticas
    const stats = JSON.parse(localStorage.getItem('cybershield_stats') || '{"notes":0,"passwords":0,"messages":0}');
    stats.messages = (stats.messages || 0) + 1;
    localStorage.setItem('cybershield_stats', JSON.stringify(stats));
    
    showNotification('[ ÉXITO ] Mensaje encriptado correctamente');
}

// Desencriptar mensaje
function decryptMessage() {
    const encrypted = document.getElementById('messageToDecrypt').value.trim();
    
    if (!encrypted) {
        showNotification('[ ERROR ] No se proporcionó mensaje encriptado', 'error');
        return;
    }
    
    const masterKey = '<?php echo $_SESSION['master_key'] ?? 'cyber2024'; ?>';
    const decrypted = simpleDecrypt(encrypted, masterKey);
    document.getElementById('decryptedMessage').value = decrypted;
    
    if (decrypted.includes('[ ERROR ]')) {
        showNotification('[ ERROR ] Fallo en la desencriptación', 'error');
    } else {
        showNotification('[ ÉXITO ] Mensaje desencriptado correctamente');
    }
}

// Generar hash SHA-256
async function generateHash() {
    const input = document.getElementById('hashInput').value;
    
    if (!input) {
        showNotification('[ ERROR ] No se proporcionó cadena de entrada', 'error');
        return;
    }
    
    const encoder = new TextEncoder();
    const data = encoder.encode(input);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    
    document.getElementById('hashOutput').value = hashHex;
    showNotification('[ ÉXITO ] Hash SHA-256 generado');
}
</script>