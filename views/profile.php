<?php
// views/profile.php - Vista de perfil del usuario
?>

<div class="card">
    <h3>▓▒░ INFORMACIÓN DE LA CUENTA ░▒▓</h3>
    <div id="profileInfo" style="display: grid; gap: 15px;">
        <p style="text-align: center; color: rgba(0, 255, 65, 0.5);">
            <span style="display: inline-block; animation: pulse 1s infinite;">[ CARGANDO ]</span> Obteniendo información...
        </p>
    </div>
</div>

<div class="card">
    <h3>▓▒░ CAMBIAR CONTRASEÑA DE LA CUENTA ░▒▓</h3>
    <form id="changeAccountPasswordForm" onsubmit="changeAccountPassword(event)">
        <div class="input-group">
            <div class="terminal-prompt">CONTRASEÑA ACTUAL:</div>
            <input type="password" name="current_password" placeholder="********" required>
        </div>
        <div class="input-group">
            <div class="terminal-prompt">NUEVA CONTRASEÑA (mín. 8 caracteres):</div>
            <input type="password" name="new_password" placeholder="********" required>
        </div>
        <div class="input-group">
            <div class="terminal-prompt">CONFIRMAR NUEVA CONTRASEÑA:</div>
            <input type="password" name="confirm_password" placeholder="********" required>
        </div>
        <button type="submit" class="btn">[ ACTUALIZAR CONTRASEÑA DE CUENTA ]</button>
    </form>
</div>

<div class="card">
    <h3>▓▒░ GESTIÓN DE CONTRASEÑA DE BÓVEDA ░▒▓</h3>
    <div id="vaultPasswordSection">
        <p style="text-align: center; color: rgba(0, 255, 65, 0.5); padding: 20px;">
            Cargando...
        </p>
    </div>
</div>

<div class="card">
    <h3>▓▒░ ESTADÍSTICAS DE USO ░▒▓</h3>
    <div class="stats-grid" id="profileStats">
        <div class="stat-card">
            <div class="stat-value" id="statNotes">0</div>
            <div class="stat-label">▓ NOTAS EN BÓVEDA</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="statPasswords">0</div>
            <div class="stat-label">▓ CONTRASEÑAS ANALIZADAS</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="statMessages">0</div>
            <div class="stat-label">▓ MENSAJES ENCRIPTADOS</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="statActions">0</div>
            <div class="stat-label">▓ ACCIONES REGISTRADAS</div>
        </div>
    </div>
</div>

<div class="card" style="border-color: rgba(255, 0, 0, 0.3);">
    <h3 style="color: #ff0000; text-shadow: 0 0 10px #ff0000;">▓▒░ ZONA DE PELIGRO ░▒▓</h3>
    
    <!-- Eliminar contraseña de bóveda -->
    <div id="removeVaultPasswordSection" style="background: rgba(255, 165, 0, 0.1); border: 1px solid rgba(255, 165, 0, 0.3); padding: 20px; border-radius: 3px; margin-bottom: 20px;">
        <h4 style="color: #ffaa00; margin-bottom: 15px;">🔓 ELIMINAR CONTRASEÑA DE BÓVEDA</h4>
        <p style="color: rgba(255, 165, 0, 0.8); margin-bottom: 20px; line-height: 1.8;">
            <strong>⚠️ ADVERTENCIA:</strong> Al eliminar la contraseña de bóveda:<br>
            • La bóveda ya no requerirá contraseña adicional<br>
            • Cualquier persona con acceso a tu cuenta podrá ver tus notas<br>
            • Tus datos seguirán encriptados con tu contraseña de cuenta<br><br>
            <strong>Solo hazlo si estás seguro.</strong>
        </p>
        <button class="btn" onclick="removeVaultPassword()" style="background: rgba(255, 165, 0, 0.2); border-color: #ffaa00; color: #ffaa00;">
            [ ELIMINAR CONTRASEÑA DE BÓVEDA ]
        </button>
    </div>
    
    <!-- Eliminar cuenta -->
    <div style="background: rgba(255, 0, 0, 0.1); border: 1px solid rgba(255, 0, 0, 0.3); padding: 20px; border-radius: 3px;">
        <h4 style="color: #ff0000; margin-bottom: 15px;">💀 ELIMINAR CUENTA PERMANENTEMENTE</h4>
        <p style="color: rgba(255, 0, 0, 0.8); margin-bottom: 20px; line-height: 1.8;">
            <strong>⚠️ ADVERTENCIA EXTREMA:</strong> Eliminar tu cuenta borrará permanentemente:<br>
            • Todas tus notas encriptadas<br>
            • Todas tus imágenes<br>
            • Todo tu historial de actividad<br>
            • Todos tus datos de la cuenta<br><br>
            <strong>Esta acción NO se puede deshacer.</strong>
        </p>
        <button class="btn" onclick="deleteAccount()" style="background: rgba(255, 0, 0, 0.2); border-color: #ff0000; color: #ff0000;">
            [ ELIMINAR CUENTA PERMANENTEMENTE ]
        </button>
    </div>
</div>

<script>
// Cargar información del perfil
async function loadProfileInfo() {
    try {
        const formData = new FormData();
        formData.append('action', 'get_profile_info');
        
        const response = await fetch('api/profile.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayProfileInfo(data.data);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error(error);
        showNotification('[ ERROR ] No se pudo cargar la información', 'error');
    }
}

// Mostrar información del perfil
function displayProfileInfo(data) {
    const user = data.user;
    const stats = data.stats;
    
    // Información de la cuenta
    const profileInfo = document.getElementById('profileInfo');
    profileInfo.innerHTML = `
        <div style="background: rgba(0, 255, 255, 0.1); border: 1px solid #00ffff; padding: 15px; border-radius: 3px;">
            <div style="display: grid; gap: 10px;">
                <div style="display: flex; justify-content: space-between; padding: 10px; background: rgba(0, 0, 0, 0.3); border-radius: 3px;">
                    <span style="color: rgba(0, 255, 255, 0.7);">USUARIO:</span>
                    <span style="color: #00ff41; font-weight: bold;">${user.username}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px; background: rgba(0, 0, 0, 0.3); border-radius: 3px;">
                    <span style="color: rgba(0, 255, 255, 0.7);">EMAIL:</span>
                    <span style="color: #00ff41;">${user.email}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px; background: rgba(0, 0, 0, 0.3); border-radius: 3px;">
                    <span style="color: rgba(0, 255, 255, 0.7);">CUENTA CREADA:</span>
                    <span style="color: #00ff41;">${new Date(user.created_at).toLocaleDateString('es-PE')}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px; background: rgba(0, 0, 0, 0.3); border-radius: 3px;">
                    <span style="color: rgba(0, 255, 255, 0.7);">CONTRASEÑA DE BÓVEDA:</span>
                    <span style="color: ${user.has_vault_password ? '#00ff41' : '#ffff00'};">
                        ${user.has_vault_password ? '✓ CONFIGURADA' : '⚠ NO CONFIGURADA'}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px; background: rgba(0, 0, 0, 0.3); border-radius: 3px;">
                    <span style="color: rgba(0, 255, 255, 0.7);">NIVEL DE SEGURIDAD:</span>
                    <span style="color: #00ff41;">★★★★★ MÁXIMO</span>
                </div>
            </div>
        </div>
    `;
    
    // Estadísticas
    document.getElementById('statNotes').textContent = stats.notes_count || 0;
    document.getElementById('statPasswords').textContent = stats.passwords_checked || 0;
    document.getElementById('statMessages').textContent = stats.messages_encrypted || 0;
    document.getElementById('statActions').textContent = data.total_actions || 0;
    
    // Sección de contraseña de bóveda
    displayVaultPasswordSection(user.has_vault_password);
    
    // Mostrar u ocultar opción de eliminar contraseña de bóveda
    const removeVaultSection = document.getElementById('removeVaultPasswordSection');
    if (removeVaultSection) {
        removeVaultSection.style.display = user.has_vault_password ? 'block' : 'none';
    }
}

// Mostrar sección de contraseña de bóveda
function displayVaultPasswordSection(hasPassword) {
    const section = document.getElementById('vaultPasswordSection');
    
    if (!hasPassword) {
        section.innerHTML = `
            <div style="background: rgba(255, 255, 0, 0.1); border: 1px solid #ffff00; padding: 20px; border-radius: 3px; text-align: center; margin-bottom: 20px;">
                <p style="color: #ffff00; margin-bottom: 15px;">
                    ⚠️ <strong>NO HAS CONFIGURADO UNA CONTRASEÑA PARA LA BÓVEDA</strong><br><br>
                    Te recomendamos crear una contraseña adicional para proteger tus notas e imágenes encriptadas.
                </p>
            </div>
            <form id="setVaultPasswordForm" onsubmit="setVaultPassword(event)">
                <div class="input-group">
                    <div class="terminal-prompt">NUEVA CONTRASEÑA DE BÓVEDA (mín. 8 caracteres):</div>
                    <input type="password" name="vault_password" placeholder="********" required>
                </div>
                <div class="input-group">
                    <div class="terminal-prompt">CONFIRMAR CONTRASEÑA DE BÓVEDA:</div>
                    <input type="password" name="confirm_password" placeholder="********" required>
                </div>
                <button type="submit" class="btn">[ ESTABLECER CONTRASEÑA DE BÓVEDA ]</button>
            </form>
        `;
    } else {
        section.innerHTML = `
            <div style="background: rgba(0, 255, 65, 0.1); border: 1px solid #00ff41; padding: 15px; border-radius: 3px; text-align: center; margin-bottom: 20px;">
                <p style="color: #00ff41;">
                    ✓ <strong>CONTRASEÑA DE BÓVEDA CONFIGURADA</strong>
                </p>
            </div>
            <form id="changeVaultPasswordForm" onsubmit="changeVaultPassword(event)">
                <div class="input-group">
                    <div class="terminal-prompt">CONTRASEÑA ACTUAL DE BÓVEDA:</div>
                    <input type="password" name="current_password" placeholder="********" required>
                </div>
                <div class="input-group">
                    <div class="terminal-prompt">NUEVA CONTRASEÑA DE BÓVEDA (mín. 8 caracteres):</div>
                    <input type="password" name="new_password" placeholder="********" required>
                </div>
                <div class="input-group">
                    <div class="terminal-prompt">CONFIRMAR NUEVA CONTRASEÑA:</div>
                    <input type="password" name="confirm_password" placeholder="********" required>
                </div>
                <button type="submit" class="btn">[ CAMBIAR CONTRASEÑA DE BÓVEDA ]</button>
            </form>
        `;
    }
}

// Establecer contraseña de bóveda
async function setVaultPassword(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'set_vault_password');
    
    try {
        const response = await fetch('api/profile.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            event.target.reset();
            loadProfileInfo();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al establecer contraseña', 'error');
        console.error(error);
    }
}

// Cambiar contraseña de bóveda
async function changeVaultPassword(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'change_vault_password');
    
    try {
        const response = await fetch('api/profile.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            event.target.reset();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al cambiar contraseña', 'error');
        console.error(error);
    }
}

// Eliminar contraseña de bóveda
async function removeVaultPassword() {
    const vaultPassword = prompt('🔐 VERIFICACIÓN DE SEGURIDAD\n\nPara eliminar la contraseña de bóveda, ingresa tu contraseña de bóveda actual:');
    
    if (!vaultPassword) {
        return;
    }
    
    if (!confirm('⚠️ ¿Estás seguro?\n\nAl eliminar la contraseña de bóveda, cualquier persona con acceso a tu cuenta podrá ver tus notas e imágenes.\n\n¿Deseas continuar?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'remove_vault_password');
        formData.append('vault_password', vaultPassword);
        
        const response = await fetch('api/profile.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            loadProfileInfo();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al eliminar contraseña', 'error');
        console.error(error);
    }
}

// Cambiar contraseña de cuenta
async function changeAccountPassword(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'change_account_password');
    
    try {
        const response = await fetch('api/profile.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            event.target.reset();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al cambiar contraseña', 'error');
        console.error(error);
    }
}

// Eliminar cuenta (deshabilitado)
async function deleteAccount() {
    if (!confirm('⚠️⚠️⚠️ ÚLTIMA ADVERTENCIA ⚠️⚠️⚠️\n\nEstás a punto de ELIMINAR PERMANENTEMENTE tu cuenta.\n\nTODOS tus datos serán borrados:\n• Notas encriptadas\n• Imágenes\n• Historial\n• Configuración\n\n¿ESTÁS ABSOLUTAMENTE SEGURO?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete_account');
        
        const response = await fetch('api/profile.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            setTimeout(() => {
                window.location.href = 'logout.php';
            }, 2000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al procesar solicitud', 'error');
        console.error(error);
    }
}

// Cargar información al iniciar
document.addEventListener('DOMContentLoaded', function() {
    loadProfileInfo();
});
</script>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}
</style>