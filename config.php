<?php
// config.php - Configuración general del sistema con seguridad mejorada
session_start();

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cybershield');

// Configuración de la aplicación
define('APP_NAME', 'CyberShield');
define('APP_VERSION', '2.4.7');

// Configuración de seguridad
define('ENCRYPTION_METHOD', 'AES-256-CBC');
define('PBKDF2_ITERATIONS', 100000);
define('PBKDF2_KEY_LENGTH', 32);
define('PBKDF2_ALGORITHM', 'sha256');

// Zona horaria
date_default_timezone_set('America/Lima');

// Función para conectar a la base de datos
function getDBConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $conn;
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// Redireccionar si no está logueado
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Generar clave de encriptación derivada de la clave maestra usando PBKDF2
function deriveEncryptionKey($masterPassword, $salt) {
    return hash_pbkdf2(
        PBKDF2_ALGORITHM,
        $masterPassword,
        $salt,
        PBKDF2_ITERATIONS,
        PBKDF2_KEY_LENGTH,
        true
    );
}

// Encriptar datos usando AES-256-CBC
function encryptData($data, $masterPassword, $salt) {
    $key = deriveEncryptionKey($masterPassword, $salt);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENCRYPTION_METHOD));
    
    $encrypted = openssl_encrypt($data, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    
    if ($encrypted === false) {
        return false;
    }
    
    // Concatenar IV + datos encriptados y codificar en base64
    return base64_encode($iv . $encrypted);
}

// Desencriptar datos usando AES-256-CBC
function decryptData($encryptedData, $masterPassword, $salt) {
    $key = deriveEncryptionKey($masterPassword, $salt);
    $data = base64_decode($encryptedData);
    
    if ($data === false) {
        return false;
    }
    
    $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    
    $decrypted = openssl_decrypt($encrypted, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    
    return $decrypted;
}

// Generar salt aleatorio
function generateSalt($length = 32) {
    return bin2hex(random_bytes($length));
}

// Hashear contraseña con Argon2id (o bcrypt si no está disponible)
function hashPassword($password) {
    if (defined('PASSWORD_ARGON2ID')) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 2
        ]);
    } else {
        // Fallback a bcrypt
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}

// Verificar contraseña
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Sanitizar entrada
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificar token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Respuesta JSON
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}
?>