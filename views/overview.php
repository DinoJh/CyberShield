<?php
// views/overview.php - Vista de resumen del dashboard
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value" id="notesCount">0</div>
        <div class="stat-label">▓ BÓVEDA ENCRIPTADA</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" id="passwordsChecked">0</div>
        <div class="stat-label">▓ CONTRASEÑAS ANALIZADAS</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" id="messagesEncrypted">0</div>
        <div class="stat-label">▓ MENSAJES ASEGURADOS</div>
    </div>
</div>

<div class="card">
    <h3>▓▒░ AVISO DE SEGURIDAD DIARIO ░▒▓</h3>
    <p id="securityTip" style="color: rgba(0, 255, 65, 0.8); line-height: 1.8; font-size: 0.95em;"></p>
</div>

<div class="card">
    <h3>▓▒░ ACTIVIDAD RECIENTE ░▒▓</h3>
    <div id="recentActivity">
        <p style="color: rgba(0, 255, 65, 0.5); text-align: center; padding: 20px;">
            [ SISTEMA INICIADO ] Sesión activa desde: <?php echo date('d/m/Y H:i:s'); ?>
        </p>
    </div>
</div>

<div class="card">
    <h3>▓▒░ PROTOCOLOS DE SEGURIDAD ACTIVOS ░▒▓</h3>
    <div style="display: grid; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #00ff41;">
            <span style="color: #00ff41; font-size: 1.5em;">✓</span>
            <span style="color: rgba(0, 255, 65, 0.8);">ENCRIPTACIÓN XOR + BASE64 ACTIVA</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #00ff41;">
            <span style="color: #00ff41; font-size: 1.5em;">✓</span>
            <span style="color: rgba(0, 255, 65, 0.8);">ANÁLISIS DE FUERZA DE CONTRASEÑAS</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #00ff41;">
            <span style="color: #00ff41; font-size: 1.5em;">✓</span>
            <span style="color: rgba(0, 255, 65, 0.8);">BÓVEDA DE DATOS PROTEGIDA</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #ffff00;">
            <span style="color: #ffff00; font-size: 1.5em;">⚠</span>
            <span style="color: rgba(255, 255, 0, 0.8);">COMUNICACIÓN SEGURA (EN DESARROLLO)</span>
        </div>
    </div>
</div>

<script>
// Cargar estadísticas desde localStorage
document.addEventListener('DOMContentLoaded', function() {
    const stats = JSON.parse(localStorage.getItem('cybershield_stats') || '{"notes":0,"passwords":0,"messages":0}');
    
    document.getElementById('notesCount').textContent = stats.notes || 0;
    document.getElementById('passwordsChecked').textContent = stats.passwords || 0;
    document.getElementById('messagesEncrypted').textContent = stats.messages || 0;
});
</script>