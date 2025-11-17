<?php
// api/vault.php - API para gestionar la bóveda encriptada con AES-256
require_once '../config.php';
requireLogin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$userId = $_SESSION['user_id'];
$masterKey = $_SESSION['master_key'] ?? '';
$masterSalt = $_SESSION['master_salt'] ?? '';

if (empty($masterKey) || empty($masterSalt)) {
    jsonResponse(false, '[ ERROR ] Clave maestra no disponible');
}

try {
    $conn = getDBConnection();
    
    switch ($action) {
        case 'save':
            // Guardar nota encriptada
            $title = sanitizeInput($_POST['title'] ?? '');
            $content = $_POST['content'] ?? '';
            
            if (empty($title) || empty($content)) {
                jsonResponse(false, '[ ERROR ] Título y contenido son obligatorios');
            }
            
            // Encriptar contenido con AES-256-CBC
            $encryptedContent = encryptData($content, $masterKey, $masterSalt);
            
            if ($encryptedContent === false) {
                jsonResponse(false, '[ ERROR ] Fallo al encriptar el contenido');
            }
            
            // Guardar en base de datos
            $stmt = $conn->prepare("INSERT INTO vault_notes (user_id, title, encrypted_content, content_type) VALUES (?, ?, ?, 'text')");
            $stmt->execute([$userId, $title, $encryptedContent]);
            
            $noteId = $conn->lastInsertId();
            
            // Actualizar estadísticas
            $stmt = $conn->prepare("UPDATE user_stats SET notes_count = notes_count + 1 WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Log de seguridad
            $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent) VALUES (?, 'CREATE_NOTE', ?, ?)");
            $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
            
            jsonResponse(true, '[ ÉXITO ] Nota encriptada y almacenada', ['id' => $noteId]);
            break;
            
        case 'list':
            // Listar todas las notas del usuario (sin desencriptar)
            $stmt = $conn->prepare("SELECT id, title, content_type, created_at, updated_at FROM vault_notes WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
            $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            jsonResponse(true, 'Notas obtenidas', ['notes' => $notes]);
            break;
            
        case 'view':
            // Ver una nota específica (desencriptada)
            $noteId = intval($_POST['note_id'] ?? $_GET['note_id'] ?? 0);
            
            if ($noteId <= 0) {
                jsonResponse(false, '[ ERROR ] ID de nota inválido');
            }
            
            $stmt = $conn->prepare("SELECT id, title, encrypted_content, content_type, created_at FROM vault_notes WHERE id = ? AND user_id = ?");
            $stmt->execute([$noteId, $userId]);
            $note = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$note) {
                jsonResponse(false, '[ ERROR ] Nota no encontrada');
            }
            
            // Desencriptar contenido
            $decryptedContent = decryptData($note['encrypted_content'], $masterKey, $masterSalt);
            
            if ($decryptedContent === false) {
                jsonResponse(false, '[ ERROR ] Fallo al desencriptar - Clave incorrecta');
            }
            
            $note['content'] = $decryptedContent;
            unset($note['encrypted_content']);
            
            // Log de seguridad
            $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent, details) VALUES (?, 'VIEW_NOTE', ?, ?, ?)");
            $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', "Note ID: $noteId"]);
            
            jsonResponse(true, 'Nota desencriptada', ['note' => $note]);
            break;
            
        case 'delete':
            // Eliminar nota
            $noteId = intval($_POST['note_id'] ?? 0);
            
            if ($noteId <= 0) {
                jsonResponse(false, '[ ERROR ] ID de nota inválido');
            }
            
            $stmt = $conn->prepare("DELETE FROM vault_notes WHERE id = ? AND user_id = ?");
            $stmt->execute([$noteId, $userId]);
            
            if ($stmt->rowCount() > 0) {
                // Actualizar estadísticas
                $stmt = $conn->prepare("UPDATE user_stats SET notes_count = GREATEST(0, notes_count - 1) WHERE user_id = ?");
                $stmt->execute([$userId]);
                
                // Log de seguridad
                $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent, details) VALUES (?, 'DELETE_NOTE', ?, ?, ?)");
                $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', "Note ID: $noteId"]);
                
                jsonResponse(true, '[ ELIMINADO ] Nota borrada de la bóveda');
            } else {
                jsonResponse(false, '[ ERROR ] Nota no encontrada o sin permisos');
            }
            break;
            
        case 'upload_image':
            // Subir y encriptar imagen (futuro)
            if (!isset($_FILES['image'])) {
                jsonResponse(false, '[ ERROR ] No se recibió ninguna imagen');
            }
            
            $file = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                jsonResponse(false, '[ ERROR ] Tipo de archivo no permitido');
            }
            
            if ($file['size'] > 5 * 1024 * 1024) { // 5MB máximo
                jsonResponse(false, '[ ERROR ] Archivo muy grande (máx. 5MB)');
            }
            
            // Leer contenido de la imagen
            $imageContent = file_get_contents($file['tmp_name']);
            $title = sanitizeInput($_POST['title'] ?? 'Imagen_' . date('Y-m-d_H-i-s'));
            
            // Encriptar imagen
            $encryptedContent = encryptData($imageContent, $masterKey, $masterSalt);
            
            if ($encryptedContent === false) {
                jsonResponse(false, '[ ERROR ] Fallo al encriptar la imagen');
            }
            
            // Guardar en base de datos
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $stmt = $conn->prepare("INSERT INTO vault_notes (user_id, title, encrypted_content, content_type, file_extension) VALUES (?, ?, ?, 'image', ?)");
            $stmt->execute([$userId, $title, $encryptedContent, $extension]);
            
            $noteId = $conn->lastInsertId();
            
            // Actualizar estadísticas
            $stmt = $conn->prepare("UPDATE user_stats SET notes_count = notes_count + 1 WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Log de seguridad
            $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent) VALUES (?, 'UPLOAD_IMAGE', ?, ?)");
            $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
            
            jsonResponse(true, '[ ÉXITO ] Imagen encriptada y almacenada', ['id' => $noteId]);
            break;
            
        case 'download_image':
            // Descargar y desencriptar imagen
            $noteId = intval($_GET['note_id'] ?? 0);
            
            if ($noteId <= 0) {
                die('ID de nota inválido');
            }
            
            $stmt = $conn->prepare("SELECT title, encrypted_content, file_extension FROM vault_notes WHERE id = ? AND user_id = ? AND content_type = 'image'");
            $stmt->execute([$noteId, $userId]);
            $note = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$note) {
                die('Imagen no encontrada');
            }
            
            // Desencriptar imagen
            $decryptedContent = decryptData($note['encrypted_content'], $masterKey, $masterSalt);
            
            if ($decryptedContent === false) {
                die('Fallo al desencriptar la imagen');
            }
            
            // Enviar imagen
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp'
            ];
            
            $mimeType = $mimeTypes[$note['file_extension']] ?? 'application/octet-stream';
            
            header('Content-Type: ' . $mimeType);
            header('Content-Disposition: inline; filename="' . $note['title'] . '.' . $note['file_extension'] . '"');
            echo $decryptedContent;
            exit();
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