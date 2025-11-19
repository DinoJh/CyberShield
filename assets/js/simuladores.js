// simuladores.js - Lógica de los simuladores de CyberShield

// Variables globales para los simuladores
let currentSimulator = null;
let simulatorIntervals = {};

// Abrir modal de simulador
function openSimulator(type) {
    const modal = document.getElementById('simulatorModal');
    const content = document.getElementById('simulatorContent');
    
    if (!modal || !content) {
        console.error('Modal o content no encontrado');
        return;
    }
    
    // Limpiar intervalos anteriores
    clearAllIntervals();
    
    const simulators = {
        bruteforce: getBruteForceSimulator(),
        phishing: getPhishingSimulator(),
        network: getNetworkSimulator(),
        encryption: getEncryptionSimulator(),
        firewall: getFirewallSimulator(),
        malware: getMalwareSimulator()
    };
    
    if (simulators[type]) {
        content.innerHTML = simulators[type];
        modal.classList.add('active');
        currentSimulator = type;
        
        // Inicializar el simulador específico
        if (type === 'malware') initMalwareSimulator();
    }
}

// Cerrar modal
function closeSimulator() {
    clearAllIntervals();
    const modal = document.getElementById('simulatorModal');
    if (modal) {
        modal.classList.remove('active');
    }
    currentSimulator = null;
}

// Limpiar todos los intervalos
function clearAllIntervals() {
    Object.values(simulatorIntervals).forEach(interval => {
        if (interval) clearInterval(interval);
    });
    simulatorIntervals = {};
}

// ========== SIMULADOR DE FUERZA BRUTA ==========
function getBruteForceSimulator() {
    return `
        <h2 class="simulator-title">🔓 SIMULADOR DE ATAQUE DE FUERZA BRUTA</h2>
        
        <div class="sim-hint">
            <strong>📚 ¿Qué es un ataque de fuerza bruta?</strong><br>
            Es un método que prueba sistemáticamente todas las combinaciones posibles de caracteres hasta encontrar la contraseña correcta. 
            La complejidad aumenta exponencialmente con cada carácter adicional.
        </div>

        <div class="simulator-interface">
            <label class="sim-label">CONTRASEÑA OBJETIVO:</label>
            <input type="text" id="targetPassword" class="sim-input" placeholder="Ingresa una contraseña de prueba" value="abc123">
            
            <label class="sim-label">VELOCIDAD DE ATAQUE:</label>
            <select id="attackSpeed" class="sim-select">
                <option value="100">Lento (100 intentos/seg)</option>
                <option value="1000" selected>Medio (1,000 intentos/seg)</option>
                <option value="10000">Rápido (10,000 intentos/seg)</option>
                <option value="100000">Muy Rápido (100,000 intentos/seg)</option>
                <option value="1000000">Supercomputadora (1,000,000 intentos/seg)</option>
            </select>

            <div style="margin: 20px 0;">
                <button class="sim-btn" onclick="startBruteForce()">[ INICIAR ATAQUE ]</button>
                <button class="sim-btn sim-btn-danger" onclick="stopBruteForce()">[ DETENER ]</button>
            </div>

            <div class="sim-progress-bar">
                <div class="sim-progress-fill" id="bruteProgress"></div>
            </div>

            <div class="sim-stats-grid">
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="bruteAttempts">0</div>
                    <div class="sim-stat-label">INTENTOS</div>
                </div>
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="bruteTime">0.0s</div>
                    <div class="sim-stat-label">TIEMPO</div>
                </div>
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="bruteEstimate">-</div>
                    <div class="sim-stat-label">ESTIMACIÓN</div>
                </div>
            </div>

            <div class="terminal-output" id="bruteOutput">
                <div class="terminal-line" style="color: #00ffff;">> Sistema de ataque listo. Ingresa una contraseña y presiona INICIAR ATAQUE</div>
                <div class="terminal-line" style="color: #ffff00;">> TIP: Prueba con diferentes longitudes para ver la diferencia exponencial</div>
            </div>
        </div>

        <div class="sim-hint sim-hint-warning">
            <strong>⚠️ LECCIÓN DE SEGURIDAD:</strong><br>
            • Contraseña de 6 caracteres (solo minúsculas): ~5 minutos<br>
            • Contraseña de 8 caracteres (mixta): ~5 horas<br>
            • Contraseña de 12 caracteres (compleja): ~200 años<br>
            • Contraseña de 16 caracteres (compleja): ~34,000 siglos<br>
            <strong>RECOMENDACIÓN:</strong> Usa mínimo 12 caracteres con mayúsculas, minúsculas, números y símbolos
        </div>
    `;
}

let bruteForceData = { interval: null, startTime: 0, attempts: 0 };

function startBruteForce() {
    const target = document.getElementById('targetPassword').value;
    const speed = parseInt(document.getElementById('attackSpeed').value);
    
    if (!target) {
        alert('[ ERROR ] Ingresa una contraseña primero');
        return;
    }

    stopBruteForce();
    
    bruteForceData.attempts = 0;
    bruteForceData.startTime = Date.now();
    const output = document.getElementById('bruteOutput');
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    
    output.innerHTML = '<div class="terminal-line" style="color: #ff0000;">> [ ATAQUE INICIADO ]</div>';
    output.innerHTML += `<div class="terminal-line" style="color: #ffff00;">> Objetivo: ${target.length} caracteres | Velocidad: ${speed.toLocaleString()} intentos/seg</div>`;
    
    const totalCombinations = Math.pow(chars.length, target.length);
    const estimatedSeconds = totalCombinations / speed;
    document.getElementById('bruteEstimate').textContent = formatTime(estimatedSeconds);

    bruteForceData.interval = setInterval(() => {
        bruteForceData.attempts += speed / 10;
        const elapsed = (Date.now() - bruteForceData.startTime) / 1000;
        const progress = Math.min((bruteForceData.attempts / totalCombinations) * 100, 99.9);
        
        document.getElementById('bruteAttempts').textContent = Math.floor(bruteForceData.attempts).toLocaleString();
        document.getElementById('bruteTime').textContent = elapsed.toFixed(1) + 's';
        document.getElementById('bruteProgress').style.width = progress + '%';
        
        if (Math.random() < 0.15) {
            const attempt = generateRandomString(target.length, chars);
            output.innerHTML += `<div class="terminal-line" style="color: rgba(255,255,0,0.6);">> Probando: ${attempt}</div>`;
            output.scrollTop = output.scrollHeight;
        }
        
        if (Math.random() < 0.002 || bruteForceData.attempts >= totalCombinations * 0.6) {
            stopBruteForce();
            document.getElementById('bruteProgress').style.width = '100%';
            output.innerHTML += `<div class="terminal-line" style="color: #00ff41; font-size: 1.2em;">> ✓ [ CONTRASEÑA ENCONTRADA ] "${target}"</div>`;
            output.innerHTML += `<div class="terminal-line" style="color: #00ffff;">> Total de intentos: ${Math.floor(bruteForceData.attempts).toLocaleString()}</div>`;
            output.innerHTML += `<div class="terminal-line" style="color: #ff00ff;">> Tiempo total: ${elapsed.toFixed(2)}s</div>`;
            output.innerHTML += `<div class="terminal-line" style="color: #ffff00;">> [ ANÁLISIS ] Esta contraseña es VULNERABLE a ataques automatizados</div>`;
            output.scrollTop = output.scrollHeight;
        }
    }, 100);
    
    simulatorIntervals.bruteforce = bruteForceData.interval;
}

function stopBruteForce() {
    if (bruteForceData.interval) {
        clearInterval(bruteForceData.interval);
        bruteForceData.interval = null;
    }
}

// ========== SIMULADOR DE PHISHING ==========
function getPhishingSimulator() {
    return `
        <h2 class="simulator-title">🎣 DETECTOR DE PHISHING</h2>
        
        <div class="sim-hint">
            <strong>📚 ¿Qué es el Phishing?</strong><br>
            Es una técnica de ingeniería social donde atacantes se hacen pasar por entidades legítimas para robar 
            información personal, contraseñas o datos financieros. El 91% de los ciberataques comienzan con un email de phishing.
        </div>

        <div class="simulator-interface">
            <h3 style="color: #00ff41; margin-bottom: 20px;">📧 ANALIZA EL SIGUIENTE CORREO:</h3>
            
            <div class="phishing-email">
                <div class="email-header">
                    <strong>De:</strong> <span class="suspicious" title="Dominio falso con número 1">seguridad@paypa1.com</span><br>
                    <strong>Para:</strong> usuario@correo.com<br>
                    <strong>Asunto:</strong> ¡Acción Urgente Requerida! Tu cuenta será suspendida<br>
                    <strong>Fecha:</strong> ${new Date().toLocaleDateString('es-ES')}
                </div>
                <div class="email-body">
                    Estimado cliente,<br><br>
                    Hemos detectado <span class="suspicious" title="Urgencia falsa para generar pánico">actividad inusual en tu cuenta</span>. 
                    Por tu seguridad, necesitamos que <span class="suspicious" title="Solicitud de acción inmediata">verifiques tu identidad inmediatamente</span> 
                    o tu cuenta será <span class="suspicious" title="Amenaza de consecuencias">cerrada permanentemente en 24 horas</span>.<br><br>
                    <span class="suspicious" title="URL sospechosa con dominio .tk">Haz clic aquí: http://paypa1-security-verify.tk/login</span><br><br>
                    Gracias por tu comprensión,<br>
                    <span class="suspicious" title="Firma genérica sin datos específicos">Equipo de Seguridad</span>
                </div>
            </div>
            
            <div style="margin: 20px 0; text-align: center;">
                <button class="sim-btn sim-btn-danger" onclick="analyzePhishing(1)">[ ESTE ES PHISHING ]</button>
                <button class="sim-btn" onclick="analyzePhishing(0)">[ ESTE ES LEGÍTIMO ]</button>
            </div>

            <div class="terminal-output" id="phishingOutput">
                <div class="terminal-line" style="color: #00ffff;">> Analiza el correo cuidadosamente. Busca señales sospechosas.</div>
                <div class="terminal-line" style="color: #ffff00;">> PISTA: Pasa el cursor sobre los textos resaltados en rojo</div>
            </div>
        </div>

        <div class="sim-hint sim-hint-danger">
            <strong>🚨 SEÑALES DE PHISHING:</strong><br>
            ✓ Remitente con dominio sospechoso o alterado (paypa1 vs paypal)<br>
            ✓ Urgencia excesiva y amenazas de cierre de cuenta<br>
            ✓ Enlaces que no coinciden con el dominio oficial<br>
            ✓ Dominios gratuitos o sospechosos (.tk, .ml, .ga)<br>
            ✓ Solicitudes de información personal sensible<br>
            ✓ Errores ortográficos o gramaticales<br>
            ✓ Saludos genéricos ("Estimado cliente" vs tu nombre)
        </div>

        <div class="simulator-interface" style="margin-top: 20px;">
            <h3 style="color: #00ff41;">🔍 VERIFICA UN ENLACE SOSPECHOSO:</h3>
            <input type="text" id="urlToCheck" class="sim-input" placeholder="Pega aquí una URL para analizar">
            <button class="sim-btn" onclick="checkURL()">[ ANALIZAR URL ]</button>
            <div id="urlResult" style="margin-top: 15px; padding: 15px; border-radius: 3px;"></div>
        </div>
    `;
}

function analyzePhishing(isPhishing) {
    const output = document.getElementById('phishingOutput');
    output.innerHTML = '';
    
    if (isPhishing === 1) {
        output.innerHTML = `
            <div class="terminal-line" style="color: #00ff41; font-size: 1.1em;">✓ ¡CORRECTO! Este es un intento de phishing</div>
            <div class="terminal-line" style="color: #ffff00;">> [ SEÑALES DETECTADAS ]:</div>
            <div class="terminal-line" style="color: #ff6600;">  1. Dominio falso: "paypa1.com" (con número 1 en lugar de letra 'l')</div>
            <div class="terminal-line" style="color: #ff6600;">  2. Urgencia excesiva: "24 horas" para crear pánico</div>
            <div class="terminal-line" style="color: #ff6600;">  3. URL sospechosa: dominio .tk (usado frecuentemente en fraudes)</div>
            <div class="terminal-line" style="color: #ff6600;">  4. Amenazas de cierre de cuenta</div>
            <div class="terminal-line" style="color: #ff6600;">  5. Firma genérica sin información de contacto real</div>
            <div class="terminal-line" style="color: #00ffff; margin-top: 10px;">> [ RECOMENDACIÓN ]: NUNCA hagas clic en enlaces de emails sospechosos</div>
        `;
    } else {
        output.innerHTML = `
            <div class="terminal-line" style="color: #ff0000; font-size: 1.1em;">✗ INCORRECTO. Este ES un intento de phishing</div>
            <div class="terminal-line" style="color: #ffff00;">> Revisa las señales de alerta mencionadas en la sección de ayuda</div>
            <div class="terminal-line" style="color: #ff6600;">> Siempre verifica el remitente, el dominio y los enlaces antes de hacer clic</div>
        `;
    }
}

function checkURL() {
    const url = document.getElementById('urlToCheck').value.trim();
    const result = document.getElementById('urlResult');
    
    if (!url) {
        result.style.background = 'rgba(255, 0, 0, 0.2)';
        result.style.border = '1px solid #ff0000';
        result.innerHTML = '<strong style="color: #ff0000;">[ ERROR ]</strong> Ingresa una URL primero';
        return;
    }
    
    const suspiciousIndicators = [];
    const urlLower = url.toLowerCase();
    
    if (urlLower.match(/\.(tk|ml|ga|cf|gq)($|\/)/)) {
        suspiciousIndicators.push('🚩 Dominio de nivel superior sospechoso');
    }
    if (urlLower.match(/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/)) {
        suspiciousIndicators.push('🚩 Usa dirección IP en lugar de dominio');
    }
    if (urlLower.includes('@')) {
        suspiciousIndicators.push('🚩 Contiene @ (técnica de ofuscación)');
    }
    if (url.length > 75) {
        suspiciousIndicators.push('🚩 URL excesivamente larga');
    }
    if (!urlLower.startsWith('https://')) {
        suspiciousIndicators.push('🚩 No usa protocolo HTTPS seguro');
    }
    
    if (suspiciousIndicators.length > 0) {
        result.style.background = 'rgba(255, 0, 0, 0.2)';
        result.style.border = '1px solid #ff0000';
        result.innerHTML = `
            <strong style="color: #ff0000;">⚠️ URL SOSPECHOSA</strong><br><br>
            ${suspiciousIndicators.map(i => '• ' + i).join('<br>')}
        `;
    } else {
        result.style.background = 'rgba(0, 255, 0, 0.2)';
        result.style.border = '1px solid #00ff41';
        result.innerHTML = '<strong style="color: #00ff41;">✓ URL PARECE SEGURA</strong><br>No se detectaron indicadores obvios';
    }
}

// ========== SIMULADOR DE ESCANEO DE REDES ==========
function getNetworkSimulator() {
    return `
        <h2 class="simulator-title">🌐 SIMULADOR DE ESCANEO DE REDES</h2>
        
        <div class="sim-hint">
            <strong>📚 ¿Qué es el escaneo de redes?</strong><br>
            Es el proceso de identificar dispositivos activos en una red y detectar puertos abiertos o vulnerabilidades.
        </div>

        <div class="simulator-interface">
            <div style="margin: 20px 0;">
                <button class="sim-btn" onclick="startNetworkScan()">[ INICIAR ESCANEO ]</button>
                <button class="sim-btn sim-btn-danger" onclick="stopNetworkScan()">[ DETENER ]</button>
            </div>

            <div class="network-diagram" id="networkDiagram">
                <div class="network-node" id="node1">🖥️</div>
                <div class="network-node" id="node2">💻</div>
                <div class="network-node" id="node3">📱</div>
                <div class="network-node" id="node4">🖨️</div>
                <div class="network-node" id="node5">📡</div>
            </div>

            <div class="sim-progress-bar">
                <div class="sim-progress-fill" id="scanProgress"></div>
            </div>

            <div class="terminal-output" id="networkOutput">
                <div class="terminal-line" style="color: #00ffff;">> Sistema de escaneo inicializado</div>
                <div class="terminal-line" style="color: #00ff41;">> Red: 192.168.1.0/24</div>
                <div class="terminal-line" style="color: #ffff00;">> Presiona INICIAR ESCANEO</div>
            </div>
        </div>

        <div class="sim-hint sim-hint-success">
            <strong>🛡️ PROTECCIÓN DE RED:</strong><br>
            • Usa un firewall robusto<br>
            • Cambia contraseñas predeterminadas<br>
            • Mantén firmware actualizado<br>
            • Desactiva servicios innecesarios<br>
            • Usa cifrado WPA3 para WiFi
        </div>
    `;
}

let networkScanData = { interval: null, currentNode: 0 };

function startNetworkScan() {
    stopNetworkScan();
    const output = document.getElementById('networkOutput');
    const nodes = ['node1', 'node2', 'node3', 'node4', 'node5'];
    networkScanData.currentNode = 0;
    
    output.innerHTML = '<div class="terminal-line" style="color: #00ff41;">> [ ESCANEO INICIADO ]</div>';
    
    const devices = ['PC Windows', 'MacBook Pro', 'Samsung Galaxy', 'Impresora HP', 'Router TP-Link'];
    const ports = [[80, 443, 445], [22, 80, 443], [5555], [9100, 631], [80, 443, 23]];
    const vulnerabilities = [
        'SMB sin parchar (EternalBlue)',
        'SSH con contraseña débil',
        'Puerto ADB abierto',
        'Credenciales predeterminadas',
        'Telnet activo - CRÍTICO'
    ];
    
    networkScanData.interval = setInterval(() => {
        if (networkScanData.currentNode < nodes.length) {
            document.getElementById(nodes[networkScanData.currentNode]).classList.add('active');
            
            const ip = `192.168.1.${100 + networkScanData.currentNode}`;
            output.innerHTML += `<div class="terminal-line" style="color: #00ffff;"><br>> Dispositivo: ${ip}</div>`;
            output.innerHTML += `<div class="terminal-line" style="color: rgba(0,255,65,0.8);">   Tipo: ${devices[networkScanData.currentNode]}</div>`;
            output.innerHTML += `<div class="terminal-line" style="color: rgba(0,255,65,0.7);">   Puertos: ${ports[networkScanData.currentNode].join(', ')}</div>`;
            
            if (Math.random() > 0.3) {
                output.innerHTML += `<div class="terminal-line" style="color: #ff0000;">   ⚠️ ${vulnerabilities[networkScanData.currentNode]}</div>`;
            }
            
            output.scrollTop = output.scrollHeight;
            
            setTimeout(() => {
                document.getElementById(nodes[networkScanData.currentNode]).classList.remove('active');
            }, 1200);
            
            networkScanData.currentNode++;
            const progress = (networkScanData.currentNode / nodes.length) * 100;
            document.getElementById('scanProgress').style.width = progress + '%';
        } else {
            stopNetworkScan();
            output.innerHTML += `<div class="terminal-line" style="color: #00ff41;"><br>> [ COMPLETADO ] ${nodes.length} dispositivos</div>`;
            output.scrollTop = output.scrollHeight;
        }
    }, 2000);
    
    simulatorIntervals.network = networkScanData.interval;
}

function stopNetworkScan() {
    if (networkScanData.interval) {
        clearInterval(networkScanData.interval);
        networkScanData.interval = null;
    }
}

// ========== SIMULADOR DE CIFRADO ==========
function getEncryptionSimulator() {
    return `
        <h2 class="simulator-title">🔐 SIMULADOR DE CIFRADO</h2>
        
        <div class="sim-hint">
            <strong>📚 ¿Qué es el cifrado?</strong><br>
            Proceso de convertir información legible en formato codificado que solo puede descifrarse con la clave correcta.
        </div>

        <div class="simulator-interface">
            <label class="sim-label">MENSAJE A CIFRAR:</label>
            <textarea id="encMessage" class="sim-textarea" placeholder="Escribe tu mensaje secreto">Información confidencial</textarea>
            
            <label class="sim-label">NIVEL DE CIFRADO:</label>
            <select id="encLevel" class="sim-select">
                <option value="weak">ROT13 - Obsoleto</option>
                <option value="medium">Base64 - Solo codificación</option>
                <option value="strong" selected>AES-256 - Recomendado</option>
            </select>

            <div style="margin: 20px 0;">
                <button class="sim-btn" onclick="simulateEncryption()">[ CIFRAR ]</button>
                <button class="sim-btn" onclick="simulateDecryption()">[ DESCIFRAR ]</button>
            </div>

            <div class="terminal-output" id="encOutput">
                <div class="terminal-line" style="color: #00ffff;">> Sistema de cifrado listo</div>
            </div>

            <div class="sim-stats-grid">
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="encStrength">-</div>
                    <div class="sim-stat-label">SEGURIDAD</div>
                </div>
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="crackTime">-</div>
                    <div class="sim-stat-label">TIEMPO ROMPER</div>
                </div>
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="encBits">-</div>
                    <div class="sim-stat-label">BITS</div>
                </div>
            </div>
        </div>

        <div class="sim-hint sim-hint-success">
            <strong>🔒 TIPOS DE CIFRADO:</strong><br>
            • ROT13: Muy débil, solo histórico<br>
            • Base64: NO es cifrado real<br>
            • AES-256: Estándar militar - Inquebrantable
        </div>
    `;
}

let lastEncrypted = '';

function simulateEncryption() {
    const message = document.getElementById('encMessage').value;
    const level = document.getElementById('encLevel').value;
    const output = document.getElementById('encOutput');
    
    if (!message) {
        alert('Ingresa un mensaje primero');
        return;
    }
    
    let encrypted, strength, crackTime, bits;
    
    output.innerHTML = '<div class="terminal-line" style="color: #00ffff;">> Cifrando...</div>';
    
    if (level === 'weak') {
        encrypted = rot13(message);
        strength = '🔴 DÉBIL';
        crackTime = 'Milisegundos';
        bits = '~4 bits';
        document.getElementById('encStrength').style.color = '#ff0000';
    } else if (level === 'medium') {
        encrypted = btoa(unescape(encodeURIComponent(message)));
        strength = '🟡 NULO';
        crackTime = 'Instantáneo';
        bits = '0 bits';
        document.getElementById('encStrength').style.color = '#ffff00';
    } else {
        encrypted = simulateAES(message);
        strength = '🟢 FUERTE';
        crackTime = '10³⁸ años';
        bits = '256 bits';
        document.getElementById('encStrength').style.color = '#00ff41';
    }
    
    lastEncrypted = encrypted;
    
    document.getElementById('encStrength').textContent = strength;
    document.getElementById('crackTime').textContent = crackTime;
    document.getElementById('encBits').textContent = bits;
    
    output.innerHTML += `<div class="terminal-line" style="color: rgba(0,255,65,0.7);">> Original: ${message.substring(0, 50)}</div>`;
    output.innerHTML += `<div class="terminal-line" style="color: #ff00ff;">> Cifrado: ${encrypted}</div>`;
    output.innerHTML += `<div class="terminal-line" style="color: #00ff41;">> [ COMPLETADO ]</div>`;
}

function simulateDecryption() {
    const output = document.getElementById('encOutput');
    if (!lastEncrypted) {
        output.innerHTML += '<div class="terminal-line" style="color: #ff6600;">> ERROR: Primero cifra un mensaje</div>';
        return;
    }
    output.innerHTML += '<div class="terminal-line" style="color: #00ff41;">> ✓ Descifrado con éxito</div>';
}

function rot13(str) {
    return str.replace(/[a-zA-Z]/g, char => {
        const start = char <= 'Z' ? 65 : 97;
        return String.fromCharCode(start + (char.charCodeAt(0) - start + 13) % 26);
    });
}

function simulateAES(str) {
    let result = '';
    for (let i = 0; i < Math.ceil(str.length * 1.5); i++) {
        result += Math.floor(Math.random() * 16).toString(16);
    }
    return result.toUpperCase();
}

// ========== SIMULADOR DE FIREWALL ==========
function getFirewallSimulator() {
    return `
        <h2 class="simulator-title">🛡️ SIMULADOR DE FIREWALL</h2>
        
        <div class="sim-hint">
            <strong>📚 ¿Qué es un Firewall?</strong><br>
            Barrera de seguridad que monitorea y controla el tráfico de red basándose en reglas predefinidas.
        </div>

        <div class="simulator-interface">
            <div class="terminal-output" id="firewallLog" style="max-height: 300px;">
                <div class="terminal-line" style="color: #00ffff;">> Firewall inicializado</div>
            </div>

            <div style="margin: 20px 0;">
                <button class="sim-btn" onclick="startFirewallSim()">[ INICIAR MONITOREO ]</button>
                <button class="sim-btn sim-btn-danger" onclick="stopFirewallSim()">[ DETENER ]</button>
            </div>

            <div class="sim-stats-grid">
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="fwAllowed" style="color: #00ff41;">0</div>
                    <div class="sim-stat-label">PERMITIDOS</div>
                </div>
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="fwBlocked" style="color: #ff0000;">0</div>
                    <div class="sim-stat-label">BLOQUEADOS</div>
                </div>
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="fwThreats" style="color: #ff00ff;">0</div>
                    <div class="sim-stat-label">AMENAZAS</div>
                </div>
            </div>

            <h3 style="color: #00ff41; margin-top: 30px;">⚙️ REGLAS:</h3>
            <div class="firewall-rules">
                <div class="firewall-rule">
                    <input type="checkbox" id="ruleHTTP" checked>
                    <label for="ruleHTTP">Permitir HTTP/HTTPS (80, 443)</label>
                </div>
                <div class="firewall-rule">
                    <input type="checkbox" id="ruleSSH">
                    <label for="ruleSSH">Permitir SSH (22)</label>
                </div>
                <div class="firewall-rule">
                    <input type="checkbox" id="ruleFTP">
                    <label for="ruleFTP">Permitir FTP (21)</label>
                </div>
                <div class="firewall-rule">
                    <input type="checkbox" id="ruleUnknown">
                    <label for="ruleUnknown" style="color: #ff0000;">Permitir desconocidos 🚨</label>
                </div>
            </div>
        </div>

        <div class="sim-hint sim-hint-warning">
            <strong>⚙️ MEJORES PRÁCTICAS:</strong><br>
            • Solo permite lo necesario<br>
            • Denegar por defecto<br>
            • Revisa logs regularmente<br>
            • Usa firewall en red y dispositivos
        </div>
    `;
}

let firewallData = { interval: null, allowed: 0, blocked: 0, threats: 0 };

function startFirewallSim() {
    stopFirewallSim();
    const output = document.getElementById('firewallLog');
    
    const connections = [
        { type: 'HTTP', port: 80, rule: 'ruleHTTP', safe: true, icon: '🌐' },
        { type: 'HTTPS', port: 443, rule: 'ruleHTTP', safe: true, icon: '🔒' },
        { type: 'SSH', port: 22, rule: 'ruleSSH', safe: true, icon: '🔐' },
        { type: 'FTP', port: 21, rule: 'ruleFTP', safe: false, icon: '📁' },
        { type: 'Unknown', port: 1337, rule: 'ruleUnknown', safe: false, threat: true, icon: '❓' },
        { type: 'SQL', port: 3306, rule: 'ruleUnknown', safe: false, threat: true, icon: '🗃️' }
    ];
    
    output.innerHTML = '<div class="terminal-line" style="color: #00ff41;">> [ MONITOREO INICIADO ]</div>';
    
    firewallData.interval = setInterval(() => {
        const conn = connections[Math.floor(Math.random() * connections.length)];
        const ip = `${Math.floor(Math.random() * 256)}.${Math.floor(Math.random() * 256)}.${Math.floor(Math.random() * 256)}.${Math.floor(Math.random() * 256)}`;
        
        const allowed = document.getElementById(conn.rule).checked;
        const timestamp = new Date().toLocaleTimeString('es-ES');
        
        if (allowed) {
            firewallData.allowed++;
            output.innerHTML += `<div class="terminal-line" style="color: #00ff41;">[${timestamp}] ✓ ${conn.icon} ${conn.type} desde ${ip}:${conn.port}</div>`;
            if (conn.threat) {
                firewallData.threats++;
                output.innerHTML += `<div class="terminal-line" style="color: #ff0000;">  🚨 Conexión peligrosa permitida</div>`;
            }
        } else {
            firewallData.blocked++;
            output.innerHTML += `<div class="terminal-line" style="color: #ff6600;">[${timestamp}] ✗ ${conn.icon} ${conn.type} desde ${ip}:${conn.port}</div>`;
        }
        
        output.scrollTop = output.scrollHeight;
        
        document.getElementById('fwAllowed').textContent = firewallData.allowed;
        document.getElementById('fwBlocked').textContent = firewallData.blocked;
        document.getElementById('fwThreats').textContent = firewallData.threats;
    }, 1500);
    
    simulatorIntervals.firewall = firewallData.interval;
}

function stopFirewallSim() {
    if (firewallData.interval) {
        clearInterval(firewallData.interval);
        firewallData.interval = null;
    }
}

// ========== SIMULADOR DE MALWARE ==========
function getMalwareSimulator() {
    return `
        <h2 class="simulator-title">🦠 SIMULADOR DE ANÁLISIS DE MALWARE</h2>
        
        <div class="sim-hint">
            <strong>📚 ¿Qué es el Malware?</strong><br>
            Software malicioso diseñado para dañar, explotar o comprometer sistemas. Incluye virus, troyanos, ransomware y más.
        </div>

        <div class="simulator-interface">
            <h3 style="color: #00ff41;">📂 ARCHIVOS DEL SISTEMA:</h3>
            
            <div id="filesList" style="margin: 20px 0;">
                <div style="color: rgba(0,255,65,0.7); text-align: center; padding: 20px;">
                    Presiona ESCANEAR SISTEMA para analizar
                </div>
            </div>

            <div style="margin: 20px 0;">
                <button class="sim-btn" onclick="scanSystem()">[ ESCANEAR SISTEMA ]</button>
                <button class="sim-btn sim-btn-danger" onclick="quarantineAll()">[ CUARENTENA ]</button>
            </div>

            <div class="terminal-output" id="malwareOutput">
                <div class="terminal-line" style="color: #00ffff;">> Sistema de análisis listo</div>
                <div class="terminal-line" style="color: #ffff00;">> Presiona ESCANEAR SISTEMA</div>
            </div>

            <div class="sim-stats-grid">
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="filesScanned">0</div>
                    <div class="sim-stat-label">ARCHIVOS</div>
                </div>
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="threatsFound" style="color: #ff0000;">0</div>
                    <div class="sim-stat-label">AMENAZAS</div>
                </div>
                <div class="sim-stat-box">
                    <div class="sim-stat-value" id="systemHealth" style="color: #00ff41;">100%</div>
                    <div class="sim-stat-label">SALUD</div>
                </div>
            </div>
        </div>

        <div class="sim-hint sim-hint-danger">
            <strong>🚨 PREVENCIÓN:</strong><br>
            ✓ Mantén antivirus actualizado<br>
            ✓ No descargues de fuentes no confiables<br>
            ✓ No abras adjuntos sospechosos<br>
            ✓ Mantén sistema actualizado<br>
            ✓ Haz respaldos regulares
        </div>

        <div class="simulator-interface" style="margin-top: 20px;">
            <h3 style="color: #00ff41;">🦠 TIPOS DE MALWARE:</h3>
            <div style="background: rgba(0,0,0,0.3); padding: 20px; line-height: 2; border: 1px solid rgba(0,255,65,0.3); border-radius: 5px;">
                <strong style="color: #ff0000;">🦠 Virus:</strong> Se replica y adjunta a archivos<br>
                <strong style="color: #ff6600;">🐴 Troyano:</strong> Se disfraza de software legítimo<br>
                <strong style="color: #ff00ff;">🔒 Ransomware:</strong> Cifra archivos y exige rescate<br>
                <strong style="color: #ffff00;">👁️ Spyware:</strong> Espía y roba información<br>
                <strong style="color: #00ffff;">🪱 Gusano:</strong> Se propaga automáticamente<br>
                <strong style="color: #ffa500;">📢 Adware:</strong> Muestra anuncios no deseados
            </div>
        </div>
    `;
}

function initMalwareSimulator() {
    // Función vacía, ya inicializamos en el HTML
}

function scanSystem() {
    const filesList = document.getElementById('filesList');
    const output = document.getElementById('malwareOutput');
    
    const files = [
        { name: 'documento.pdf', safe: true, icon: '📄', size: '2.3 MB' },
        { name: 'foto.jpg', safe: true, icon: '🖼️', size: '4.1 MB' },
        { name: 'factura.exe', safe: false, icon: '⚠️', threat: 'Troyano', size: '156 KB' },
        { name: 'update.bat', safe: false, icon: '⚠️', threat: 'Script malicioso', size: '8 KB' },
        { name: 'video.mp4', safe: true, icon: '🎬', size: '45 MB' },
        { name: 'crack.exe', safe: false, icon: '⚠️', threat: 'Ransomware', size: '3.2 MB' },
        { name: 'readme.txt', safe: true, icon: '📝', size: '2 KB' },
        { name: 'loader.dll', safe: false, icon: '⚠️', threat: 'Backdoor', size: '512 KB' }
    ];
    
    filesList.innerHTML = '';
    output.innerHTML = '<div class="terminal-line" style="color: #00ffff;">> [ ESCANEO INICIADO ]</div>';
    
    let scanned = 0;
    let threats = 0;
    
    files.forEach((file, index) => {
        setTimeout(() => {
            scanned++;
            const color = file.safe ? '#00ff41' : '#ff0000';
            const status = file.safe ? 'LIMPIO' : `INFECTADO - ${file.threat}`;
            
            filesList.innerHTML += `
                <div class="file-item ${file.safe ? 'safe' : 'infected'}">
                    <div>
                        <span style="font-size: 1.5em;">${file.icon}</span>
                        <strong style="margin-left: 10px;">${file.name}</strong>
                        <span style="color: rgba(0,255,65,0.5); margin-left: 10px; font-size: 0.85em;">(${file.size})</span>
                    </div>
                    <span style="color: ${color}; font-weight: bold;">${status}</span>
                </div>
            `;
            
            output.innerHTML += `<div class="terminal-line" style="color: ${color};">> [${scanned}/${files.length}] ${file.name} - ${status}</div>`;
            output.scrollTop = output.scrollHeight;
            
            if (!file.safe) threats++;
            
            document.getElementById('filesScanned').textContent = scanned;
            document.getElementById('threatsFound').textContent = threats;
            const health = Math.max(0, 100 - (threats * 12));
            document.getElementById('systemHealth').textContent = health + '%';
            document.getElementById('systemHealth').style.color = health > 70 ? '#00ff41' : health > 40 ? '#ffff00' : '#ff0000';
            
            if (scanned === files.length) {
                output.innerHTML += `<div class="terminal-line" style="color: #00ff41; margin-top: 10px;"><br>> [ COMPLETADO ] ${threats} amenazas</div>`;
                output.scrollTop = output.scrollHeight;
            }
        }, index * 600);
    });
}

function quarantineAll() {
    const output = document.getElementById('malwareOutput');
    output.innerHTML += '<div class="terminal-line" style="color: #00ffff; margin-top: 15px;"><br>> [ CUARENTENA INICIADA ]</div>';
    
    setTimeout(() => {
        output.innerHTML += '<div class="terminal-line" style="color: #00ff41;">> ✓ Amenazas neutralizadas</div>';
        output.innerHTML += '<div class="terminal-line" style="color: #00ff41;">> ✓ Sistema limpiado</div>';
        document.getElementById('threatsFound').textContent = '0';
        document.getElementById('systemHealth').textContent = '100%';
        document.getElementById('systemHealth').style.color = '#00ff41';
        output.scrollTop = output.scrollHeight;
    }, 2000);
}

// ========== FUNCIONES AUXILIARES ==========

function generateRandomString(length, chars) {
    let result = '';
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

function formatTime(seconds) {
    if (seconds < 1) return (seconds * 1000).toFixed(0) + ' ms';
    if (seconds < 60) return seconds.toFixed(1) + ' seg';
    if (seconds < 3600) return (seconds / 60).toFixed(1) + ' min';
    if (seconds < 86400) return (seconds / 3600).toFixed(1) + ' hrs';
    if (seconds < 31536000) return (seconds / 86400).toFixed(0) + ' días';
    return (seconds / 31536000).toFixed(0) + ' años';
}

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(event) {
    const modal = document.getElementById('simulatorModal');
    if (event.target === modal) {
        closeSimulator();
    }
});

// Limpiar al cerrar la página
window.addEventListener('beforeunload', function() {
    clearAllIntervals();
});