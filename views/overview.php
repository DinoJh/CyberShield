<?php
// views/overview.php - Vista de resumen del dashboard con estadísticas reales
$conn = getDBConnection();
$userId = $_SESSION['user_id'];

// Obtener estadísticas del usuario
$stmt = $conn->prepare("SELECT passwords_checked, messages_encrypted, notes_count FROM user_stats WHERE user_id = ?");
$stmt->execute([$userId]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stats) {
    // Crear registro de estadísticas si no existe
    $stmt = $conn->prepare("INSERT INTO user_stats (user_id) VALUES (?)");
    $stmt->execute([$userId]);
    $stats = ['passwords_checked' => 0, 'messages_encrypted' => 0, 'notes_count' => 0];
}

// Obtener actividad reciente
$stmt = $conn->prepare("SELECT action, created_at, details FROM security_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$userId]);
$recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);

$actionLabels = [
    'LOGIN' => '🔐 Inicio de sesión',
    'CREATE_NOTE' => '📝 Nota creada',
    'VIEW_NOTE' => '👁️ Nota visualizada',
    'DELETE_NOTE' => '🗑️ Nota eliminada',
    'UPLOAD_IMAGE' => '🖼️ Imagen subida',
    'ENCRYPT_MESSAGE' => '🔒 Mensaje encriptado'
];
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?php echo $stats['notes_count']; ?></div>
        <div class="stat-label">▓ BÓVEDA ENCRIPTADA</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo $stats['passwords_checked']; ?></div>
        <div class="stat-label">▓ CONTRASEÑAS ANALIZADAS</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo $stats['messages_encrypted']; ?></div>
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
        <?php if (count($recentActivity) > 0): ?>
            <?php foreach ($recentActivity as $activity): ?>
                <div style="display: flex; justify-content: space-between; padding: 12px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #00ff41; margin-bottom: 10px;">
                    <span style="color: #00ff41;">
                        <?php echo $actionLabels[$activity['action']] ?? $activity['action']; ?>
                        <?php if ($activity['details']): ?>
                            <span style="color: rgba(0, 255, 65, 0.5); font-size: 0.85em;"> - <?php echo htmlspecialchars($activity['details']); ?></span>
                        <?php endif; ?>
                    </span>
                    <span style="color: rgba(0, 255, 255, 0.5); font-size: 0.85em;">
                        <?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: rgba(0, 255, 65, 0.5); text-align: center; padding: 20px;">
                [ SISTEMA INICIADO ] Sesión activa desde: <?php echo date('d/m/Y H:i:s'); ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <h3>▓▒░ PROTOCOLOS DE SEGURIDAD ACTIVOS ░▒▓</h3>
    <div style="display: grid; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #00ff41;">
            <span style="color: #00ff41; font-size: 1.5em;">✓</span>
            <span style="color: rgba(0, 255, 65, 0.8);">ENCRIPTACIÓN AES-256-CBC ACTIVA</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #00ff41;">
            <span style="color: #00ff41; font-size: 1.5em;">✓</span>
            <span style="color: rgba(0, 255, 65, 0.8);">DERIVACIÓN DE CLAVE PBKDF2 (100K ITERACIONES)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #00ff41;">
            <span style="color: #00ff41; font-size: 1.5em;">✓</span>
            <span style="color: rgba(0, 255, 65, 0.8);">AUTENTICACIÓN ARGON2ID / BCRYPT</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #00ff41;">
            <span style="color: #00ff41; font-size: 1.5em;">✓</span>
            <span style="color: rgba(0, 255, 65, 0.8);">PROTECCIÓN CONTRA FUERZA BRUTA (5 INTENTOS)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #00ff41;">
            <span style="color: #00ff41; font-size: 1.5em;">✓</span>
            <span style="color: rgba(0, 255, 65, 0.8);">LOGS DE AUDITORÍA DE SEGURIDAD</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid #ffff00;">
            <span style="color: #ffff00; font-size: 1.5em;">⚠</span>
            <span style="color: rgba(255, 255, 0, 0.8);">COMUNICACIÓN ENTRE USUARIOS (EN DESARROLLO)</span>
        </div>
    </div>
</div>

<div class="card">
    <h3>▓▒░ INFORMACIÓN DE TU CUENTA ░▒▓</h3>
    <div style="background: rgba(0, 255, 255, 0.1); border: 1px solid #00ffff; padding: 15px; border-radius: 3px;">
        <p style="color: #00ffff; line-height: 1.8; font-size: 0.9em;">
            <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?><br>
            <strong>Última conexión:</strong> <?php echo date('d/m/Y H:i:s'); ?><br>
            <strong>Nivel de seguridad:</strong> <span style="color: #00ff41;">★★★★★ MÁXIMO</span><br>
            <strong>Algoritmo de encriptación:</strong> AES-256-CBC<br>
            <strong>Hash de contraseña:</strong> Argon2id / bcrypt<br>
            <strong>Salt único:</strong> <?php echo substr($_SESSION['master_salt'], 0, 16); ?>...
        </p>
    </div>
</div>