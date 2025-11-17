<?php
// index.php - Dashboard principal
require_once 'config.php';
requireLogin();

// Tips de seguridad
$securityTips = [
    "> NUNCA compartir contraseñas por email o mensajes sin encriptar",
    "> HABILITAR autenticación de dos factores en todas las cuentas críticas",
    "> ACTUALIZAR contraseñas cada 90 días para máxima seguridad",
    "> USAR contraseñas únicas para cada servicio - nunca reutilizar",
    "> VERIFICAR certificados SSL antes de ingresar datos sensibles",
    "> MANTENER sistema y software actualizados para parchar vulnerabilidades",
    "> IMPLEMENTAR gestor de contraseñas para almacenamiento seguro",
    "> MONITOREAR registros de actividad de cuentas para comportamiento sospechoso",
    "> RESPALDAR datos encriptados en ubicaciones seguras sin conexión",
    "> EVITAR WiFi público para transacciones sensibles"
];
$randomTip = $securityTips[array_rand($securityTips)];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Command Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="matrix-bg" id="matrix"></div>
    <div class="scanlines"></div>

    <div style="padding: 20px; max-width: 1400px; margin: 0 auto; position: relative; z-index: 2;">
        <div class="header">
            <h2>
                <span class="status-indicator"></span>
                ▓▒░ CYBERSHIELD COMMAND CENTER ░▒▓
            </h2>
            <a href="logout.php" class="logout-btn" style="text-decoration: none;">[ DESCONECTAR ]</a>
        </div>

        <div class="nav-tabs">
            <button class="active" onclick="switchTab('overview')">[ RESUMEN ]</button>
            <button onclick="switchTab('password')">[ CONTRASEÑAS ]</button>
            <button onclick="switchTab('vault')">[ BÓVEDA ]</button>
            <button onclick="switchTab('messages')">[ COMUNICACIÓN SEGURA ]</button>
            <button onclick="window.location.href='simuladores.php'" style="background: linear-gradient(45deg, rgba(255, 0, 255, 0.2), rgba(0, 255, 255, 0.2)); border-color: #ff00ff;">[ APRENDE A DEFENDERTE ]</button>
        </div>

        <!-- Tab: Resumen -->
        <div class="tab-content" id="overview" style="display: block;">
            <?php include 'views/overview.php'; ?>
        </div>

        <!-- Tab: Contraseñas -->
        <div class="tab-content" id="password" style="display: none;">
            <?php include 'views/passwords.php'; ?>
        </div>

        <!-- Tab: Bóveda -->
        <div class="tab-content" id="vault" style="display: none;">
            <?php include 'views/vault.php'; ?>
        </div>

        <!-- Tab: Comunicación Segura -->
        <div class="tab-content" id="messages" style="display: none;">
            <?php include 'views/messages.php'; ?>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Mostrar tip de seguridad aleatorio
        document.addEventListener('DOMContentLoaded', function() {
            const tipElement = document.getElementById('securityTip');
            if (tipElement) {
                tipElement.textContent = '<?php echo $randomTip; ?>';
            }
        });

        // Fix para el sistema de pestañas
        function switchTab(tabName) {
            // Ocultar todas las pestañas
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.style.display = 'none');
            
            // Remover clase active de todos los botones
            const buttons = document.querySelectorAll('.nav-tabs button');
            buttons.forEach(btn => {
                if (!btn.hasAttribute('onclick') || !btn.getAttribute('onclick').includes('window.location')) {
                    btn.classList.remove('active');
                }
            });
            
            // Mostrar la pestaña seleccionada
            const activeTab = document.getElementById(tabName);
            if (activeTab) {
                activeTab.style.display = 'block';
            }
            
            // Agregar clase active al botón clickeado
            event.target.classList.add('active');
        }
    </script>
</body>
</html>