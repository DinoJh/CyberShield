<?php
// views/passwords.php - Vista de gestión de contraseñas
?>

<div class="card">
    <h3>▓▒░ ANALIZADOR DE FUERZA DE CONTRASEÑA ░▒▓</h3>
    <div class="input-group">
        <div class="terminal-prompt">INGRESE CONTRASEÑA PARA ANÁLISIS:</div>
        <input type="password" id="checkPassword" placeholder="contraseña_prueba_123">
        <div class="strength-meter">
            <div class="strength-bar" id="strengthBar"></div>
        </div>
        <p id="strengthText" style="margin-top: 10px; color: rgba(0, 255, 65, 0.7);"></p>
    </div>
    <div id="passwordFeedback" style="margin-top: 15px;"></div>
</div>

<div class="card">
    <h3>▓▒░ GENERADOR DE CONTRASEÑAS SEGURAS ░▒▓</h3>
    <div class="input-group">
        <label style="color: #00ffff;">LONGITUD: <span id="lengthValue" style="color: #00ff41;">16</span> CARACTERES</label>
        <input type="range" class="range-input" id="passwordLength" min="8" max="32" value="16">
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(0, 255, 65, 0.3); border-radius: 3px;">
            <input type="checkbox" id="includeUpper" checked style="width: 18px; height: 18px; cursor: pointer; accent-color: #00ff41;">
            <label for="includeUpper" style="cursor: pointer; font-size: 0.9em;">MAYÚSCULAS [A-Z]</label>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(0, 255, 65, 0.3); border-radius: 3px;">
            <input type="checkbox" id="includeLower" checked style="width: 18px; height: 18px; cursor: pointer; accent-color: #00ff41;">
            <label for="includeLower" style="cursor: pointer; font-size: 0.9em;">MINÚSCULAS [a-z]</label>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(0, 255, 65, 0.3); border-radius: 3px;">
            <input type="checkbox" id="includeNumbers" checked style="width: 18px; height: 18px; cursor: pointer; accent-color: #00ff41;">
            <label for="includeNumbers" style="cursor: pointer; font-size: 0.9em;">NÚMEROS [0-9]</label>
        </div>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(0, 255, 65, 0.3); border-radius: 3px;">
            <input type="checkbox" id="includeSymbols" checked style="width: 18px; height: 18px; cursor: pointer; accent-color: #00ff41;">
            <label for="includeSymbols" style="cursor: pointer; font-size: 0.9em;">SÍMBOLOS [!@#$]</label>
        </div>
    </div>
    <div style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
        <input type="text" id="generatedPassword" readonly style="flex: 1; min-width: 200px; padding: 12px; background: rgba(0, 0, 0, 0.5); border: 1px solid #00ff41; border-radius: 3px; color: #00ff41; font-family: 'Fira Code', monospace; font-size: 0.95em;">
        <button class="btn-small" onclick="generatePassword()">[ GENERAR ]</button>
        <button class="btn-small" onclick="copyToClipboard('generatedPassword')">[ COPIAR ]</button>
    </div>
</div>

<div class="card">
    <h3>▓▒░ PROTOCOLOS DE SEGURIDAD RECOMENDADOS ░▒▓</h3>
    <p style="color: rgba(0, 255, 65, 0.7); line-height: 1.8; font-size: 0.9em;">
        > LONGITUD MÍNIMA DE CONTRASEÑA: 12 CARACTERES<br>
        > USAR COMBINACIÓN DE MAYÚSCULAS, MINÚSCULAS, NÚMEROS Y SÍMBOLOS<br>
        > EVITAR PALABRAS DEL DICCIONARIO Y PATRONES<br>
        > HABILITAR AUTENTICACIÓN DE DOS FACTORES<br>
        > CONTRASEÑA ÚNICA POR CUENTA<br>
        > ROTACIÓN REGULAR DE CONTRASEÑAS (90 DÍAS)<br>
        > NUNCA COMPARTIR CREDENCIALES POR CANALES INSEGUROS
    </p>
</div>

<script>
// Listener para análisis de contraseña
document.getElementById('checkPassword')?.addEventListener('input', function() {
    const password = this.value;
    const result = checkPasswordStrength(password);
    
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    if (strengthBar && strengthText) {
        strengthBar.className = 'strength-bar strength-' + result.level;
        strengthText.textContent = result.text;
        strengthText.style.color = result.color;
        
        // Actualizar estadísticas
        const stats = JSON.parse(localStorage.getItem('cybershield_stats') || '{"notes":0,"passwords":0,"messages":0}');
        stats.passwords = (stats.passwords || 0) + 1;
        localStorage.setItem('cybershield_stats', JSON.stringify(stats));
    }
});

// Estilo para inputs de rango
document.querySelector('.range-input').style.cssText = 'width: 100%; margin-top: 10px; accent-color: #00ff41;';
</script>