<?php
// simuladores.php - Página principal de simuladores educativos
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
    <link rel="stylesheet" href="assets/css/simuladores.css">
</head>
<body>
    <div class="matrix-bg" id="matrix"></div>
    <div class="scanlines"></div>

    <div style="padding: 20px; max-width: 1400px; margin: 0 auto; position: relative; z-index: 2;">
        <div class="header">
            <h2>
                <span class="status-indicator"></span>
                ▓▒▒ LABORATORIO DE CIBERSEGURIDAD ▒▒▓
            </h2>
            <div style="display: flex; gap: 10px;">
                <a href="index.php" class="btn-small" style="text-decoration: none; display: inline-block;">[ VOLVER AL DASHBOARD ]</a>
                <a href="logout.php" class="logout-btn" style="text-decoration: none;">[ DESCONECTAR ]</a>
            </div>
        </div>

        <div class="card">
            <h3>▓▒▒ SIMULADORES INTERACTIVOS ▒▒▓</h3>
            <p style="color: rgba(0, 255, 65, 0.8); line-height: 1.8; margin-bottom: 30px;">
                Aprende sobre las amenazas cibernéticas más comunes a través de simuladores interactivos.
                Estos módulos te ayudarán a entender cómo funcionan los ataques y cómo defenderte de ellos.
            </p>

            <div class="stats-grid">
                <!-- SIMULADORES FUNCIONALES -->
                <div class="stat-card sim-card-active" onclick="openSimulator('bruteforce')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🔓</div>
                    <div class="stat-label">FUERZA BRUTA</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Simula ataques de fuerza bruta y comprende la importancia de contraseñas fuertes
                    </p>
                    <div style="margin-top: 10px; background: rgba(0, 255, 65, 0.2); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #00ff41;">
                        ✓ DISPONIBLE
                    </div>
                </div>

                <div class="stat-card sim-card-active" onclick="openSimulator('phishing')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🎣</div>
                    <div class="stat-label">DETECCIÓN DE PHISHING</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Aprende a identificar correos fraudulentos y protégete del engaño
                    </p>
                    <div style="margin-top: 10px; background: rgba(0, 255, 65, 0.2); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #00ff41;">
                        ✓ DISPONIBLE
                    </div>
                </div>

                <div class="stat-card sim-card-active" onclick="openSimulator('network')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🌐</div>
                    <div class="stat-label">ESCANEO DE REDES</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Visualiza cómo funcionan los escaneos de red y detección de vulnerabilidades
                    </p>
                    <div style="margin-top: 10px; background: rgba(0, 255, 65, 0.2); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #00ff41;">
                        ✓ DISPONIBLE
                    </div>
                </div>

                <div class="stat-card sim-card-active" onclick="openSimulator('encryption')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🔐</div>
                    <div class="stat-label">CIFRADO DE DATOS</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Experimenta con diferentes niveles de cifrado y su resistencia
                    </p>
                    <div style="margin-top: 10px; background: rgba(0, 255, 65, 0.2); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #00ff41;">
                        ✓ DISPONIBLE
                    </div>
                </div>

                <div class="stat-card sim-card-active" onclick="openSimulator('firewall')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🛡️</div>
                    <div class="stat-label">FIREWALL INTERACTIVO</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Gestiona reglas de firewall y aprende a proteger tu red
                    </p>
                    <div style="margin-top: 10px; background: rgba(0, 255, 65, 0.2); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #00ff41;">
                        ✓ DISPONIBLE
                    </div>
                </div>

                <div class="stat-card sim-card-active" onclick="openSimulator('malware')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🦠</div>
                    <div class="stat-label">ANÁLISIS DE MALWARE</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Detecta y analiza software malicioso en un entorno seguro
                    </p>
                    <div style="margin-top: 10px; background: rgba(0, 255, 65, 0.2); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #00ff41;">
                        ✓ DISPONIBLE
                    </div>
                </div>

                <!-- SIMULADORES EN DESARROLLO -->
                <div class="stat-card" style="cursor: pointer; position: relative; opacity: 0.7;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de Ingeniería Social')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🎭</div>
                    <div class="stat-label">INGENIERÍA SOCIAL</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Reconoce técnicas de manipulación psicológica
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>

                <div class="stat-card" style="cursor: pointer; position: relative; opacity: 0.7;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de SQL Injection')">
                    <div style="font-size: 3em; margin-bottom: 15px;">💉</div>
                    <div class="stat-label">SQL INJECTION</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Aprende sobre inyección de código en bases de datos
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>

                <div class="stat-card" style="cursor: pointer; position: relative; opacity: 0.7;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de Ransomware')">
                    <div style="font-size: 3em; margin-bottom: 15px;">🔒</div>
                    <div class="stat-label">RANSOMWARE</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Comprende el secuestro de datos y su prevención
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>

                <div class="stat-card" style="cursor: pointer; position: relative; opacity: 0.7;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de XSS')">
                    <div style="font-size: 3em; margin-bottom: 15px;">⚡</div>
                    <div class="stat-label">CROSS-SITE SCRIPTING</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Explora vulnerabilidades de scripting en sitios web
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>

                <div class="stat-card" style="cursor: pointer; position: relative; opacity: 0.7;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de DDoS')">
                    <div style="font-size: 3em; margin-bottom: 15px;">💥</div>
                    <div class="stat-label">ATAQUES DDoS</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Entiende los ataques de denegación de servicio distribuidos
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>

                <div class="stat-card" style="cursor: pointer; position: relative; opacity: 0.7;" onclick="alert('[ PRÓXIMAMENTE ] Simulador de Man-in-the-Middle')">
                    <div style="font-size: 3em; margin-bottom: 15px;">👤</div>
                    <div class="stat-label">MAN-IN-THE-MIDDLE</div>
                    <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 10px;">
                        Aprende sobre interceptación de comunicaciones
                    </p>
                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 0, 0.3); padding: 5px 10px; border-radius: 3px; font-size: 0.7em; color: #ffff00;">
                        EN DESARROLLO
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>▓▒▒ INFORMACIÓN IMPORTANTE ▒▒▓</h3>
            <div style="background: rgba(0, 255, 255, 0.1); border: 1px solid #00ffff; padding: 20px; border-radius: 3px;">
                <p style="color: #00ffff; line-height: 1.8;">
                    <strong>[ ENTORNO DE APRENDIZAJE SEGURO ]</strong><br><br>
                    Todos los simuladores operan en un entorno controlado y seguro. Características:<br><br>
                    ✓ Entorno aislado para practicar sin riesgos<br>
                    ✓ Explicaciones detalladas de cada técnica de ataque<br>
                    ✓ Ejercicios prácticos interactivos paso a paso<br>
                    ✓ Métricas en tiempo real de seguridad<br>
                    ✓ Recomendaciones de mejores prácticas<br><br>
                    <strong>OBJETIVO:</strong> Educar sobre ciberseguridad de manera práctica y accesible para todos los niveles de conocimiento, desde principiantes hasta profesionales.
                </p>
            </div>
        </div>
    </div>

    <!-- Modal para simuladores -->
    <div class="modal" id="simulatorModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeSimulator()">[ X CERRAR ]</button>
            <div id="simulatorContent"></div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/simuladores.js"></script>
    <script>
        // Verificar que el modal existe
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Simuladores cargados correctamente');
            const modal = document.getElementById('simulatorModal');
            if (!modal) {
                console.error('Modal no encontrado!');
            } else {
                console.log('Modal encontrado correctamente');
            }
        });
    </script>
</body>
</html>