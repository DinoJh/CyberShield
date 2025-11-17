<?php
// simuladores.php - Página de simuladores educativos
require_once 'config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Simuladores de Seguridad</title>
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
                ▓▒░ SIMULADORES DE SEGURIDAD ░▒▓
            </h2>
            <div style="display: flex; gap: 10px;">
                <a href="index.php" class="btn-small" style="text-decoration: none; display: inline-block;">[ VOLVER AL DASHBOARD ]</a>
                <a href="logout.php" class="logout-btn" style="text-decoration: none;">[ DESCONECTAR ]</a>
            </div>
        </div>

        <div class="card">
            <h3>▓▒░ LABORATORIO DE CIBERSEGURIDAD ░▒▓</h3>
            <p style="color: rgba(0, 255, 65, 0.8); line-height: 1.8; margin-bottom: 30px;">
                Aprende sobre las amenazas cibernéticas más comunes a través de simuladores interactivos.
                Estos módulos te ayudarán a entender cómo funcionan los ataques y cómo defenderte de ellos.
            </p>

            <div class="stats-grid">
                <div class="stat-card" style="cursor: pointer; position: relative;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de Ataque de Fuerza Bruta')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🔓</div>
                    <div class="stat-label">FUERZA BRUTA</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Aprende cómo los atacantes intentan descifrar contraseñas
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>

                <div class="stat-card" style="cursor: pointer; position: relative;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de Detección de Phishing')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🎣</div>
                    <div class="stat-label">DETECCIÓN DE PHISHING</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Identifica correos y sitios web fraudulentos
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>

                <div class="stat-card" style="cursor: pointer; position: relative;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de Malware')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🦠</div>
                    <div class="stat-label">ANÁLISIS DE MALWARE</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Entiende cómo funciona el software malicioso
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>

                <div class="stat-card" style="cursor: pointer; position: relative;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de Ingeniería Social')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🎭</div>
                    <div class="stat-label">INGENIERÍA SOCIAL</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Reconoce técnicas de manipulación psicológica
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>

                <div class="stat-card" style="cursor: pointer; position: relative;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de Ataques de Red')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🌐</div>
                    <div class="stat-label">ATAQUES DE RED</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Comprende vulnerabilidades en redes
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>

                <div class="stat-card" style="cursor: pointer; position: relative;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de Ransomware')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🔐</div>
                    <div class="stat-label">RANSOMWARE</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Aprende sobre el secuestro de datos
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>▓▒░ INFORMACIÓN IMPORTANTE ░▒▓</h3>
            <div style="background: rgba(0, 255, 255, 0.1); border: 1px solid #00ffff; padding: 20px; border-radius: 3px;">
                <p style="color: #00ffff; line-height: 1.8;">
                    <strong>[ MÓDULO EN CONSTRUCCIÓN ]</strong><br><br>
                    Los simuladores interactivos están actualmente en desarrollo. Cada simulador proporcionará:<br><br>
                    ✓ Entorno seguro y controlado para practicar<br>
                    ✓ Explicaciones detalladas de cada técnica<br>
                    ✓ Ejercicios prácticos paso a paso<br>
                    ✓ Evaluaciones para medir tu aprendizaje<br>
                    ✓ Certificados de completación<br><br>
                    <strong>OBJETIVO:</strong> Educar sobre ciberseguridad de manera práctica y accesible para todos los niveles de conocimiento.
                </p>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>