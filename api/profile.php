<?php
// api/profile.php - API para gestión de perfil y contraseñas
require_once '../config.php';
requireLogin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$userId = $_SESSION['user_id'];

try {
    $conn = getDBConnection();
    
    switch ($action) {
        case 'set_vault_password':
            // Establecer contraseña de bóveda por primera vez
            $vaultPassword = $_POST['vault_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($vaultPassword)) {
                jsonResponse(false, '[ ERROR ] La contraseña de bóveda no puede estar vacía');
            }
            
            if (strlen($vaultPassword) < 8) {
                jsonResponse(false, '[ ERROR ] La contraseña debe tener al menos 8 caracteres');
            }
            
            if ($vaultPassword !== $confirmPassword) {
                jsonResponse(false, '[ ERROR ] Las contraseñas no coinciden');
            }
            
            // Hashear contraseña de bóveda
            $vaultHash = hashPassword($vaultPassword);
            
            // Actualizar usuario
            $stmt = $conn->prepare("UPDATE users SET vault_password_hash = ?, has_vault_password = TRUE WHERE id = ?");
            $stmt->execute([$vaultHash, $userId]);
            
            // Guardar en sesión que está desbloqueada
            $_SESSION['vault_unlocked'] = true;
            
            // Log de seguridad
            $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent) VALUES (?, 'SET_VAULT_PASSWORD', ?, ?)");
            $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
            
            jsonResponse(true, '[ ÉXITO ] Contraseña de bóveda establecida correctamente');
            break;
            
        case 'unlock_vault':
            // Desbloquear bóveda con contraseña
            $vaultPassword = $_POST['vault_password'] ?? '';
            
            if (empty($vaultPassword)) {
                jsonResponse(false, '[ ERROR ] Ingresa la contraseña de la bóveda');
            }
            
            // Obtener hash de la bóveda
            $stmt = $conn->prepare("SELECT vault_password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !$user['vault_password_hash']) {
                jsonResponse(false, '[ ERROR ] No se ha establecido una contraseña de bóveda');
            }
            
            // Verificar contraseña
            if (verifyPassword($vaultPassword, $user['vault_password_hash'])) {
                $_SESSION['vault_unlocked'] = true;
                
                // Log de seguridad
                $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent) VALUES (?, 'UNLOCK_VAULT', ?, ?)");
                $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
                
                jsonResponse(true, '[ ACCESO CONCEDIDO ] Bóveda desbloqueada');
            } else {
                // Log de intento fallido
                $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent, details) VALUES (?, 'FAILED_VAULT_UNLOCK', ?, ?, 'Contraseña incorrecta')");
                $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
                
                jsonResponse(false, '[ ACCESO DENEGADO ] Contraseña de bóveda incorrecta');
            }
            break;
            
        case 'change_vault_password':
            // Cambiar contraseña de bóveda
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($currentPassword) || empty($newPassword)) {
                jsonResponse(false, '[ ERROR ] Todos los campos son obligatorios');
            }
            
            if (strlen($newPassword) < 8) {
                jsonResponse(false, '[ ERROR ] La nueva contraseña debe tener al menos 8 caracteres');
            }
            
            if ($newPassword !== $confirmPassword) {
                jsonResponse(false, '[ ERROR ] Las contraseñas nuevas no coinciden');
            }
            
            // Obtener hash actual
            $stmt = $conn->prepare("SELECT vault_password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !$user['vault_password_hash']) {
                jsonResponse(false, '[ ERROR ] No hay contraseña de bóveda establecida');
            }
            
            // Verificar contraseña actual
            if (!verifyPassword($currentPassword, $user['vault_password_hash'])) {
                jsonResponse(false, '[ ERROR ] La contraseña actual es incorrecta');
            }
            
            // Hashear nueva contraseña (NO cambiar salt, no es necesario)
            $newVaultHash = hashPassword($newPassword);
            
            // Actualizar solo el hash
            $stmt = $conn->prepare("UPDATE users SET vault_password_hash = ? WHERE id = ?");
            $stmt->execute([$newVaultHash, $userId]);
            
            // Actualizar sesión
            $_SESSION['vault_unlocked'] = true;
            
            // Log de seguridad
            $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent) VALUES (?, 'CHANGE_VAULT_PASSWORD', ?, ?)");
            $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
            
            jsonResponse(true, '[ ÉXITO ] Contraseña de bóveda actualizada correctamente');
            break;
            
        case 'change_account_password':
            // Cambiar contraseña de la cuenta
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($currentPassword) || empty($newPassword)) {
                jsonResponse(false, '[ ERROR ] Todos los campos son obligatorios');
            }
            
            if (strlen($newPassword) < 8) {
                jsonResponse(false, '[ ERROR ] La nueva contraseña debe tener al menos 8 caracteres');
            }
            
            if ($newPassword !== $confirmPassword) {
                jsonResponse(false, '[ ERROR ] Las contraseñas nuevas no coinciden');
            }
            
            // Obtener datos del usuario
            $stmt = $conn->prepare("SELECT password_hash, master_salt FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña actual
            if (!verifyPassword($currentPassword, $user['password_hash'])) {
                jsonResponse(false, '[ ERROR ] La contraseña actual es incorrecta');
            }
            
            // RE-ENCRIPTAR TODAS LAS NOTAS CON LA NUEVA CONTRASEÑA
            try {
                // Obtener todas las notas del usuario
                $stmt = $conn->prepare("SELECT id, encrypted_content FROM vault_notes WHERE user_id = ?");
                $stmt->execute([$userId]);
                $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Desencriptar cada nota con contraseña antigua y re-encriptar con nueva
                $reencryptedNotes = [];
                foreach ($notes as $note) {
                    // Desencriptar con contraseña antigua
                    $decrypted = decryptData($note['encrypted_content'], $currentPassword, $user['master_salt']);
                    
                    if ($decrypted !== false) {
                        // Re-encriptar con contraseña nueva (usando el mismo salt)
                        $reencrypted = encryptData($decrypted, $newPassword, $user['master_salt']);
                        
                        if ($reencrypted !== false) {
                            $reencryptedNotes[] = [
                                'id' => $note['id'],
                                'content' => $reencrypted
                            ];
                        }
                    }
                }
                
                // Iniciar transacción
                $conn->beginTransaction();
                
                // Actualizar contraseña
                $newHash = hashPassword($newPassword);
                $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$newHash, $userId]);
                
                // Actualizar todas las notas re-encriptadas
                $stmt = $conn->prepare("UPDATE vault_notes SET encrypted_content = ? WHERE id = ?");
                foreach ($reencryptedNotes as $note) {
                    $stmt->execute([$note['content'], $note['id']]);
                }
                
                // Confirmar transacción
                $conn->commit();
                
                // Actualizar sesión con nueva contraseña
                $_SESSION['master_key'] = $newPassword;
                
                // Log de seguridad
                $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent, details) VALUES (?, 'CHANGE_ACCOUNT_PASSWORD', ?, ?, ?)");
                $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', count($reencryptedNotes) . ' notas re-encriptadas']);
                
                jsonResponse(true, '[ ÉXITO ] Contraseña de cuenta actualizada. Todas tus notas han sido re-encriptadas automáticamente con la nueva contraseña.');
                
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                $conn->rollBack();
                jsonResponse(false, '[ ERROR ] Error al re-encriptar las notas: ' . $e->getMessage());
            }
            break;
            
        case 'remove_vault_password':
            // Eliminar contraseña de bóveda
            $vaultPassword = $_POST['vault_password'] ?? '';
            
            if (empty($vaultPassword)) {
                jsonResponse(false, '[ ERROR ] Debes ingresar tu contraseña de bóveda para eliminarla');
            }
            
            // Obtener hash de la bóveda
            $stmt = $conn->prepare("SELECT vault_password_hash, has_vault_password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !$user['has_vault_password']) {
                jsonResponse(false, '[ ERROR ] No tienes contraseña de bóveda configurada');
            }
            
            // Verificar contraseña
            if (!verifyPassword($vaultPassword, $user['vault_password_hash'])) {
                // Log de intento fallido
                $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent, details) VALUES (?, 'FAILED_REMOVE_VAULT_PASSWORD', ?, ?, 'Contraseña incorrecta')");
                $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
                
                jsonResponse(false, '[ ERROR ] Contraseña de bóveda incorrecta');
            }
            
            // Eliminar contraseña de bóveda
            $stmt = $conn->prepare("UPDATE users SET vault_password_hash = NULL, has_vault_password = FALSE WHERE id = ?");
            $stmt->execute([$userId]);
            
            // Limpiar sesión
            $_SESSION['vault_unlocked'] = false;
            
            // Log de seguridad
            $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent) VALUES (?, 'REMOVE_VAULT_PASSWORD', ?, ?)");
            $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
            
            jsonResponse(true, '[ ÉXITO ] Contraseña de bóveda eliminada. La bóveda ya no requiere contraseña adicional.');
            break;
            
        case 'get_profile_info':
            // Obtener información del perfil
            $stmt = $conn->prepare("SELECT username, email, has_vault_password, created_at, last_login FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Obtener estadísticas
            $stmt = $conn->prepare("SELECT passwords_checked, messages_encrypted, notes_count FROM user_stats WHERE user_id = ?");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Contar logs
            $stmt = $conn->prepare("SELECT COUNT(*) as total_actions FROM security_logs WHERE user_id = ?");
            $stmt->execute([$userId]);
            $logs = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $data = [
                'user' => $user,
                'stats' => $stats,
                'total_actions' => $logs['total_actions']
            ];
            
            jsonResponse(true, 'Información obtenida', $data);
            break;
            
        case 'delete_account':
            // DESHABILITADO - Solo para decoración
            jsonResponse(false, '[ FUNCIÓN DESHABILITADA ] La eliminación de cuentas está temporalmente deshabilitada por seguridad. Contacta al administrador si necesitas eliminar tu cuenta.');
            break;
            
        default:
            jsonResponse(false, '[ ERROR ] Acción no válida');
    }
    
} catch (PDOException $e) {
    jsonResponse(false, '[ ERROR ] Error de base de datos: ' . $e->getMessage());
} catch (Exception $e) {
    jsonResponse(false, '[ ERROR ] ' . $e->getMessage());
}
?>