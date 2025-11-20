<?php
// api/messages.php - API para sistema de mensajería encriptada - CORREGIDO
require_once '../config.php';
requireLogin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$userId = $_SESSION['user_id'];

// CAMBIO IMPORTANTE: Función para generar clave compartida entre dos usuarios
function getConversationKey($user1Id, $user2Id) {
    // Asegurar orden consistente
    $ids = [$user1Id, $user2Id];
    sort($ids);
    
    // Generar clave determinística basada en los IDs de usuarios
    // NOTA: En producción, deberías usar una clave almacenada en la BD
    $keyMaterial = "cybershield_secret_" . $ids[0] . "_" . $ids[1];
    return hash('sha256', $keyMaterial);
}

function getConversationSalt($user1Id, $user2Id) {
    $ids = [$user1Id, $user2Id];
    sort($ids);
    $saltMaterial = "salt_" . $ids[0] . "_" . $ids[1];
    return hash('sha256', $saltMaterial);
}

try {
    $conn = getDBConnection();
    
    // Limpiar mensajes expirados (auto-delete)
    $stmt = $conn->prepare("UPDATE secure_messages 
                           SET is_deleted = TRUE, deleted_at = NOW() 
                           WHERE auto_delete_minutes IS NOT NULL 
                           AND TIMESTAMPDIFF(MINUTE, sent_at, NOW()) >= auto_delete_minutes 
                           AND is_deleted = FALSE");
    $stmt->execute();
    
    switch ($action) {
        case 'get_users':
            $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id != ? ORDER BY username");
            $stmt->execute([$userId]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            jsonResponse(true, 'Usuarios obtenidos', ['users' => $users]);
            break;
            
        case 'get_conversations':
            $stmt = $conn->prepare("
                SELECT 
                    c.id,
                    c.last_message_at,
                    c.last_message_preview,
                    CASE 
                        WHEN c.user1_id = ? THEN c.unread_count_user1
                        ELSE c.unread_count_user2
                    END as unread_count,
                    CASE 
                        WHEN c.user1_id = ? THEN u2.id
                        ELSE u1.id
                    END as other_user_id,
                    CASE 
                        WHEN c.user1_id = ? THEN u2.username
                        ELSE u1.username
                    END as other_username
                FROM conversations c
                LEFT JOIN users u1 ON c.user1_id = u1.id
                LEFT JOIN users u2 ON c.user2_id = u2.id
                WHERE c.user1_id = ? OR c.user2_id = ?
                ORDER BY c.last_message_at DESC
            ");
            $stmt->execute([$userId, $userId, $userId, $userId, $userId]);
            $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            jsonResponse(true, 'Conversaciones obtenidas', ['conversations' => $conversations]);
            break;
            
        case 'send_message':
            $receiverId = intval($_POST['receiver_id'] ?? 0);
            $message = $_POST['message'] ?? '';
            $autoDeleteMinutes = intval($_POST['auto_delete_minutes'] ?? 0);
            
            if ($receiverId <= 0 || empty($message)) {
                jsonResponse(false, '[ ERROR ] Datos incompletos');
            }
            
            // CAMBIO: Usar clave compartida de la conversación
            $conversationKey = getConversationKey($userId, $receiverId);
            $conversationSalt = getConversationSalt($userId, $receiverId);
            
            // Encriptar mensaje con clave compartida
            $encryptedMessage = encryptData($message, $conversationKey, $conversationSalt);
            
            if ($encryptedMessage === false) {
                jsonResponse(false, '[ ERROR ] Fallo al encriptar el mensaje');
            }
            
            // Insertar mensaje
            $autoDelete = $autoDeleteMinutes > 0 ? $autoDeleteMinutes : null;
            $stmt = $conn->prepare("INSERT INTO secure_messages (sender_id, receiver_id, encrypted_content, message_type, auto_delete_minutes) VALUES (?, ?, ?, 'text', ?)");
            $stmt->execute([$userId, $receiverId, $encryptedMessage, $autoDelete]);
            
            $messageId = $conn->lastInsertId();
            
            // Actualizar o crear conversación
            updateConversation($conn, $userId, $receiverId, substr($message, 0, 50));
            
            // Log
            $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent) VALUES (?, 'SEND_MESSAGE', ?, ?)");
            $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
            
            jsonResponse(true, '[ ÉXITO ] Mensaje enviado', ['id' => $messageId]);
            break;
            
        case 'send_file':
            $receiverId = intval($_POST['receiver_id'] ?? 0);
            $autoDeleteMinutes = intval($_POST['auto_delete_minutes'] ?? 0);
            
            if ($receiverId <= 0 || !isset($_FILES['file'])) {
                jsonResponse(false, '[ ERROR ] Datos incompletos');
            }
            
            $file = $_FILES['file'];
            
            // Determinar tipo de archivo
            $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $documentTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain'
            ];
            
            $messageType = '';
            $maxSize = 0;
            
            if (in_array($file['type'], $imageTypes)) {
                $messageType = 'image';
                $maxSize = 5 * 1024 * 1024; // 5MB
            } elseif (in_array($file['type'], $documentTypes)) {
                $messageType = 'document';
                $maxSize = 10 * 1024 * 1024; // 10MB
            } else {
                jsonResponse(false, '[ ERROR ] Tipo de archivo no permitido');
            }
            
            if ($file['size'] > $maxSize) {
                jsonResponse(false, '[ ERROR ] Archivo muy grande');
            }
            
            // CAMBIO: Usar clave compartida de la conversación
            $conversationKey = getConversationKey($userId, $receiverId);
            $conversationSalt = getConversationSalt($userId, $receiverId);
            
            // Leer y encriptar archivo
            $fileContent = file_get_contents($file['tmp_name']);
            $encryptedContent = encryptData($fileContent, $conversationKey, $conversationSalt);
            
            if ($encryptedContent === false) {
                jsonResponse(false, '[ ERROR ] Fallo al encriptar el archivo');
            }
            
            // Insertar mensaje
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $autoDelete = $autoDeleteMinutes > 0 ? $autoDeleteMinutes : null;
            
            $stmt = $conn->prepare("INSERT INTO secure_messages (sender_id, receiver_id, encrypted_content, message_type, file_extension, file_size, mime_type, auto_delete_minutes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $receiverId, $encryptedContent, $messageType, $extension, $file['size'], $file['type'], $autoDelete]);
            
            $messageId = $conn->lastInsertId();
            
            // Actualizar conversación
            $preview = $messageType === 'image' ? '📷 Imagen' : '📄 Documento';
            updateConversation($conn, $userId, $receiverId, $preview);
            
            // Log
            $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent) VALUES (?, 'SEND_FILE', ?, ?)");
            $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
            
            jsonResponse(true, '[ ÉXITO ] Archivo enviado', ['id' => $messageId]);
            break;
            
        case 'get_messages':
            $otherUserId = intval($_POST['other_user_id'] ?? $_GET['other_user_id'] ?? 0);
            $lastMessageId = intval($_GET['last_message_id'] ?? 0);
            
            if ($otherUserId <= 0) {
                jsonResponse(false, '[ ERROR ] Usuario inválido');
            }
            
            // CAMBIO: Obtener clave compartida de la conversación
            $conversationKey = getConversationKey($userId, $otherUserId);
            $conversationSalt = getConversationSalt($userId, $otherUserId);
            
            // Obtener mensajes no eliminados (o eliminados solo para el otro usuario)
            $query = "SELECT id, sender_id, receiver_id, encrypted_content, message_type, file_extension, file_size, auto_delete_minutes, sent_at, read_at, is_deleted, deleted_for_sender, deleted_for_receiver
                     FROM secure_messages 
                     WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
                     AND is_deleted = FALSE
                     AND (
                         (sender_id = ? AND deleted_for_sender = FALSE) OR
                         (receiver_id = ? AND deleted_for_receiver = FALSE)
                     )";
            
            if ($lastMessageId > 0) {
                $query .= " AND id > ?";
            }
            
            $query .= " ORDER BY sent_at ASC";
            
            $stmt = $conn->prepare($query);
            
            if ($lastMessageId > 0) {
                $stmt->execute([$userId, $otherUserId, $otherUserId, $userId, $userId, $userId, $lastMessageId]);
            } else {
                $stmt->execute([$userId, $otherUserId, $otherUserId, $userId, $userId, $userId]);
            }
            
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // CAMBIO: Desencriptar mensajes con clave compartida
            foreach ($messages as &$message) {
                if ($message['message_type'] === 'text') {
                    $decrypted = decryptData($message['encrypted_content'], $conversationKey, $conversationSalt);
                    $message['content'] = $decrypted !== false ? $decrypted : '[Error al desencriptar]';
                }
                unset($message['encrypted_content']);
                unset($message['deleted_for_sender']);
                unset($message['deleted_for_receiver']);
                
                // Marcar como leído si es receptor
                if ($message['receiver_id'] == $userId && $message['read_at'] === null) {
                    $stmtRead = $conn->prepare("UPDATE secure_messages SET read_at = NOW() WHERE id = ?");
                    $stmtRead->execute([$message['id']]);
                    $message['read_at'] = date('Y-m-d H:i:s');
                }
            }
            
            // Actualizar contador de no leídos
            updateUnreadCount($conn, $userId, $otherUserId);
            
            jsonResponse(true, 'Mensajes obtenidos', ['messages' => $messages]);
            break;
            
        case 'download_file':
            $messageId = intval($_GET['message_id'] ?? 0);
            
            if ($messageId <= 0) {
                die('ID de mensaje inválido');
            }
            
            $stmt = $conn->prepare("SELECT encrypted_content, message_type, file_extension, mime_type, sender_id, receiver_id 
                                   FROM secure_messages 
                                   WHERE id = ? AND (sender_id = ? OR receiver_id = ?) AND is_deleted = FALSE");
            $stmt->execute([$messageId, $userId, $userId]);
            $message = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$message) {
                die('Archivo no encontrado');
            }
            
            // CAMBIO: Determinar el otro usuario y usar clave compartida
            $otherUserId = ($message['sender_id'] == $userId) ? $message['receiver_id'] : $message['sender_id'];
            $conversationKey = getConversationKey($userId, $otherUserId);
            $conversationSalt = getConversationSalt($userId, $otherUserId);
            
            // Desencriptar con clave compartida
            $decrypted = decryptData($message['encrypted_content'], $conversationKey, $conversationSalt);
            
            if ($decrypted === false) {
                die('Fallo al desencriptar');
            }
            
            // Enviar archivo
            header('Content-Type: ' . $message['mime_type']);
            header('Content-Disposition: attachment; filename="file.' . $message['file_extension'] . '"');
            echo $decrypted;
            exit();
            break;
            
        case 'delete_message':
            // NUEVO: Eliminar mensaje - Ahora con opciones
            $messageId = intval($_POST['message_id'] ?? 0);
            $deleteType = $_POST['delete_type'] ?? 'me'; // 'me' o 'everyone'
            
            if ($messageId <= 0) {
                jsonResponse(false, '[ ERROR ] ID inválido');
            }
            
            // Verificar que el mensaje existe y el usuario tiene permiso
            $stmt = $conn->prepare("SELECT sender_id, receiver_id FROM secure_messages WHERE id = ? AND (sender_id = ? OR receiver_id = ?)");
            $stmt->execute([$messageId, $userId, $userId]);
            $message = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$message) {
                jsonResponse(false, '[ ERROR ] Mensaje no encontrado');
            }
            
            $isSender = $message['sender_id'] == $userId;
            
            if ($deleteType === 'everyone') {
                // Solo el emisor puede eliminar para todos
                if (!$isSender) {
                    jsonResponse(false, '[ ERROR ] Solo el emisor puede eliminar para todos');
                }
                
                // Eliminar para todos (marca como eliminado completamente)
                $stmt = $conn->prepare("UPDATE secure_messages SET is_deleted = TRUE, deleted_at = NOW() WHERE id = ?");
                $stmt->execute([$messageId]);
                
                jsonResponse(true, '[ ELIMINADO ] Mensaje borrado para todos');
                
            } else {
                // Eliminar solo para mí
                if ($isSender) {
                    $stmt = $conn->prepare("UPDATE secure_messages SET deleted_for_sender = TRUE WHERE id = ?");
                } else {
                    $stmt = $conn->prepare("UPDATE secure_messages SET deleted_for_receiver = TRUE WHERE id = ?");
                }
                $stmt->execute([$messageId]);
                
                // Si ambos lo eliminaron, marcar como completamente eliminado
                $stmt = $conn->prepare("UPDATE secure_messages SET is_deleted = TRUE, deleted_at = NOW() WHERE id = ? AND deleted_for_sender = TRUE AND deleted_for_receiver = TRUE");
                $stmt->execute([$messageId]);
                
                jsonResponse(true, '[ ELIMINADO ] Mensaje borrado para ti');
            }
            break;
            
        default:
            jsonResponse(false, '[ ERROR ] Acción no válida');
    }
    
} catch (PDOException $e) {
    jsonResponse(false, '[ ERROR ] Error de base de datos: ' . $e->getMessage());
} catch (Exception $e) {
    jsonResponse(false, '[ ERROR ] ' . $e->getMessage());
}

// Función auxiliar para actualizar conversación
function updateConversation($conn, $user1, $user2, $preview) {
    if ($user1 > $user2) {
        $temp = $user1;
        $user1 = $user2;
        $user2 = $temp;
        $incrementField = 'unread_count_user1';
    } else {
        $incrementField = 'unread_count_user2';
    }
    
    $stmt = $conn->prepare("
        INSERT INTO conversations (user1_id, user2_id, last_message_at, last_message_preview, $incrementField)
        VALUES (?, ?, NOW(), ?, 1)
        ON DUPLICATE KEY UPDATE 
            last_message_at = NOW(), 
            last_message_preview = ?,
            $incrementField = $incrementField + 1
    ");
    $stmt->execute([$user1, $user2, $preview, $preview]);
}

// Función auxiliar para actualizar contador de no leídos
function updateUnreadCount($conn, $userId, $otherUserId) {
    if ($userId > $otherUserId) {
        $temp = $userId;
        $userId = $otherUserId;
        $otherUserId = $temp;
        $resetField = 'unread_count_user2';
    } else {
        $resetField = 'unread_count_user1';
    }
    
    $stmt = $conn->prepare("UPDATE conversations SET $resetField = 0 WHERE user1_id = ? AND user2_id = ?");
    $stmt->execute([$userId, $otherUserId]);
}
?>