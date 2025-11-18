// main.js - JavaScript principal de CyberShield

// Crear efecto matrix rain
function createMatrix() {
    const matrix = document.getElementById('matrix');
    if (!matrix) return;
    
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%^&*()_+-=[]{}|;:,.<>?';
    
    for (let i = 0; i < 50; i++) {
        const column = document.createElement('div');
        column.className = 'matrix-column';
        column.style.left = Math.random() * 100 + '%';
        column.style.animationDuration = (Math.random() * 3 + 2) + 's';
        column.style.animationDelay = Math.random() * 2 + 's';
        
        let text = '';
        for (let j = 0; j < 20; j++) {
            text += chars.charAt(Math.floor(Math.random() * chars.length)) + '<br>';
        }
        column.innerHTML = text;
        matrix.appendChild(column);
    }
}

// Cambiar entre pestañas
function switchTab(tabName) {
    // Ocultar todas las pestañas
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.style.display = 'none');
    
    // Remover clase active de todos los botones de navegación
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
    
    // Agregar clase active al botón clickeado (si viene del nav-tabs)
    if (event && event.target) {
        const clickedButton = event.target.closest('button');
        if (clickedButton && clickedButton.parentElement.classList.contains('nav-tabs')) {
            clickedButton.classList.add('active');
        }
    }
    
    // Si se accede a perfil desde el header, no hay botón que marcar como activo en nav-tabs
    // Pero si queremos, podemos buscar el botón correspondiente
    if (tabName === 'profile') {
        // Remover active de todos los botones
        buttons.forEach(btn => btn.classList.remove('active'));
    }
}

// Verificar fuerza de contraseña
function checkPasswordStrength(password) {
    let score = 0;
    
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (password.length >= 16) score++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;
    
    if (score <= 2) {
        return { 
            level: 'weak', 
            text: '[ CRÍTICO ] Nivel de Vulnerabilidad: ALTO - Acción inmediata requerida', 
            color: '#ff0000' 
        };
    } else if (score <= 4) {
        return { 
            level: 'medium', 
            text: '[ ADVERTENCIA ] Nivel de Vulnerabilidad: MEDIO - Mejora recomendada', 
            color: '#ffff00' 
        };
    } else {
        return { 
            level: 'strong', 
            text: '[ SEGURO ] Nivel de Vulnerabilidad: BAJO - Contraseña cumple estándares', 
            color: '#00ff41' 
        };
    }
}

// Generar contraseña aleatoria
function generatePassword() {
    const length = document.getElementById('passwordLength').value;
    const includeUpper = document.getElementById('includeUpper').checked;
    const includeLower = document.getElementById('includeLower').checked;
    const includeNumbers = document.getElementById('includeNumbers').checked;
    const includeSymbols = document.getElementById('includeSymbols').checked;
    
    let chars = '';
    if (includeUpper) chars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if (includeLower) chars += 'abcdefghijklmnopqrstuvwxyz';
    if (includeNumbers) chars += '0123456789';
    if (includeSymbols) chars += '!@#$%^&*()_+-=[]{}|;:,.<>?';
    
    if (chars === '') {
        alert('[ ERROR ] Selecciona al menos un tipo de carácter');
        return;
    }
    
    let password = '';
    for (let i = 0; i < length; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    document.getElementById('generatedPassword').value = password;
}

// Copiar al portapapeles
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    document.execCommand('copy');
    
    const originalValue = element.value;
    element.value = '[ COPIADO AL PORTAPAPELES ]';
    setTimeout(() => {
        element.value = originalValue;
    }, 1000);
}

// Funciones de encriptación simple (XOR + Base64)
function simpleEncrypt(text, key) {
    let result = '';
    for (let i = 0; i < text.length; i++) {
        result += String.fromCharCode(text.charCodeAt(i) ^ key.charCodeAt(i % key.length));
    }
    return btoa(result);
}

function simpleDecrypt(encrypted, key) {
    try {
        const decoded = atob(encrypted);
        let result = '';
        for (let i = 0; i < decoded.length; i++) {
            result += String.fromCharCode(decoded.charCodeAt(i) ^ key.charCodeAt(i % key.length));
        }
        return result;
    } catch (e) {
        return '[ ERROR ] Desencriptación fallida - Datos o clave inválidos';
    }
}

// Generar hash SHA-256
async function generateHash() {
    const input = document.getElementById('hashInput').value;
    
    if (!input) {
        alert('[ ERROR ] No se proporcionó cadena de entrada');
        return;
    }
    
    const encoder = new TextEncoder();
    const data = encoder.encode(input);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    
    document.getElementById('hashOutput').value = hashHex;
}

// Mostrar notificación temporal
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? 'rgba(0, 255, 65, 0.2)' : 'rgba(255, 0, 0, 0.2)'};
        border: 1px solid ${type === 'success' ? '#00ff41' : '#ff0000'};
        color: ${type === 'success' ? '#00ff41' : '#ff0000'};
        border-radius: 5px;
        z-index: 9999;
        font-family: 'Fira Code', monospace;
        box-shadow: 0 0 20px ${type === 'success' ? 'rgba(0, 255, 65, 0.5)' : 'rgba(255, 0, 0, 0.5)'};
        animation: slideIn 0.3s ease-out;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Inicializar cuando carga el DOM
document.addEventListener('DOMContentLoaded', function() {
    createMatrix();
    
    // Listener para verificación de contraseña en tiempo real
    const checkPasswordInput = document.getElementById('checkPassword');
    if (checkPasswordInput) {
        checkPasswordInput.addEventListener('input', function() {
            const password = this.value;
            const result = checkPasswordStrength(password);
            
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            if (strengthBar && strengthText) {
                strengthBar.className = 'strength-bar strength-' + result.level;
                strengthText.textContent = result.text;
                strengthText.style.color = result.color;
            }
        });
    }
    
    // Listener para actualizar valor de longitud de contraseña
    const passwordLength = document.getElementById('passwordLength');
    if (passwordLength) {
        passwordLength.addEventListener('input', function() {
            const lengthValue = document.getElementById('lengthValue');
            if (lengthValue) {
                lengthValue.textContent = this.value;
            }
        });
    }
    
    // Auto-generar contraseña inicial
    if (document.getElementById('generatedPassword')) {
        generatePassword();
    }
});

// Agregar estilos para las animaciones de notificación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);