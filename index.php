<?php
// index.php - Dashboard principal
require_once 'config.php';
requireLogin();
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
            <div style="display: flex; gap: 10px; align-items: center;">
                <button onclick="switchTab('profile')" class="btn-small" style="display: flex; align-items: center; gap: 8px; background: rgba(0, 255, 255, 0.1); border-color: #00ffff; color: #00ffff;">
                    <span style="font-size: 1.2em;">👤</span>
                    [ PERFIL ]
                </button>
                <a href="logout.php" class="logout-btn" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 1.2em;">🚪</span>
                    [ DESCONECTAR ]
                </a>
            </div>
        </div>

        <div class="nav-tabs">
            <button class="active" onclick="switchTab('overview')" style="display: flex; align-items: center; gap: 6px; justify-content: center;">
                <span>📊</span> [ RESUMEN ]
            </button>
            <button onclick="switchTab('password')" style="display: flex; align-items: center; gap: 6px; justify-content: center;">
                <span>🔑</span> [ CONTRASEÑAS ]
            </button>
            <button onclick="switchTab('vault')" style="display: flex; align-items: center; gap: 6px; justify-content: center;">
                <span>🔐</span> [ BÓVEDA ]
            </button>
            <button onclick="switchTab('messages')" style="display: flex; align-items: center; gap: 6px; justify-content: center;">
                <span>📡</span> [ COMUNICACIÓN SEGURA ]
            </button>
            <button onclick="window.location.href='simuladores.php'" style="display: flex; align-items: center; gap: 6px; justify-content: center; background: linear-gradient(45deg, rgba(255, 0, 255, 0.2), rgba(0, 255, 255, 0.2)); border-color: #ff00ff;">
                <span>🎮</span> [ APRENDE A DEFENDERTE ]
            </button>
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

        <!-- Tab: Perfil -->
        <div class="tab-content" id="profile" style="display: none;">
            <?php include 'views/profile.php'; ?>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Detectar dispositivo móvil y mostrar advertencia
        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        // Mostrar advertencia en móviles (solo una vez por sesión)
        if (isMobileDevice() && !sessionStorage.getItem('mobileWarningShown')) {
            const mobileWarning = document.createElement('div');
            mobileWarning.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.98);
                z-index: 99999;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            `;
            
            mobileWarning.innerHTML = `
                <div style="
                    background: rgba(10, 14, 39, 0.95);
                    border: 2px solid #ffff00;
                    border-radius: 5px;
                    padding: 30px;
                    max-width: 500px;
                    text-align: center;
                    box-shadow: 0 0 30px rgba(255, 255, 0, 0.5);
                ">
                    <div style="font-size: 4em; margin-bottom: 20px;">📱</div>
                    <h2 style="color: #ffff00; text-shadow: 0 0 10px #ffff00; margin-bottom: 20px; font-family: 'Fira Code', monospace;">
                        ⚠️ DISPOSITIVO MÓVIL DETECTADO
                    </h2>
                    <p style="color: #00ffff; line-height: 1.8; margin-bottom: 25px; font-family: 'Fira Code', monospace; font-size: 0.95em;">
                        Este sitio está desarrollado principalmente para computadores.<br><br>
                        Para una mejor experiencia, por favor:<br>
                        <strong style="color: #00ff41;">ACTIVA EL MODO "SITIO PARA COMPUTADORA"</strong>
                    </p>
                    <div style="background: rgba(0, 255, 255, 0.1); border: 1px solid #00ffff; padding: 15px; border-radius: 3px; margin-bottom: 20px;">
                        <p style="color: rgba(0, 255, 255, 0.8); font-size: 0.85em; line-height: 1.6; font-family: 'Fira Code', monospace;">
                            📖 <strong>Cómo activarlo:</strong><br>
                            Chrome/Edge: Menú (⋮) → "Sitio de escritorio"<br>
                            Safari: (AA) → "Solicitar sitio de escritorio"<br>
                            Firefox: Menú (⋮) → "Sitio de escritorio"
                        </p>
                    </div>
                    <button onclick="sessionStorage.setItem('mobileWarningShown', 'true'); this.parentElement.parentElement.remove();" style="
                        padding: 12px 30px;
                        background: rgba(0, 255, 65, 0.2);
                        border: 2px solid #00ff41;
                        color: #00ff41;
                        font-family: 'Fira Code', monospace;
                        cursor: pointer;
                        font-size: 1em;
                        border-radius: 3px;
                        transition: all 0.3s;
                        text-transform: uppercase;
                        font-weight: bold;
                    " onmouseover="this.style.boxShadow='0 0 20px rgba(0, 255, 65, 0.6)'; this.style.background='rgba(0, 255, 65, 0.3)';" onmouseout="this.style.boxShadow=''; this.style.background='rgba(0, 255, 65, 0.2)';">
                        [ ENTENDIDO, CONTINUAR DE TODOS MODOS ]
                    </button>
                </div>
            `;
            
            document.body.appendChild(mobileWarning);
        }
    </script>
</body>
</html>