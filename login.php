<?php
// login.php - Página de inicio de sesión y registro con seguridad mejorada
require_once 'config.php';

// Si ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';
$mode = 'login'; // 'login' o 'register'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? 'login';
    
    if ($mode === 'register') {
        // REGISTRO
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validaciones
        if (empty($username) || empty($email) || empty($password)) {
            $error = '[ ERROR ] Todos los campos son obligatorios';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $error = '[ ERROR ] El usuario debe tener entre 3 y 50 caracteres';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || 
                !preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/", $email)) {
            $error = '[ ERROR ] El correo electrónico no es válido. Usa un formato correcto como ejemplo@dominio.com';
        }elseif (strlen($password) < 8) {
            $error = '[ ERROR ] La contraseña debe tener al menos 8 caracteres';
        } elseif ($password !== $confirmPassword) {
            $error = '[ ERROR ] Las contraseñas no coinciden';
        } else {
            try {
                $conn = getDBConnection();
                
                // Verificar si el usuario o email ya existen
                $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                
                if ($stmt->rowCount() > 0) {
                    $error = '[ ERROR ] Usuario o email ya registrado';
                } else {
                    // Generar salt único para el usuario
                    $masterSalt = generateSalt(32);
                    
                    // Hashear la contraseña con Argon2id
                    $passwordHash = hashPassword($password);
                    
                    // Insertar usuario
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, master_salt) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $email, $passwordHash, $masterSalt]);
                    
                    $userId = $conn->lastInsertId();
                    
                    // Crear registro de estadísticas
                    $stmt = $conn->prepare("INSERT INTO user_stats (user_id) VALUES (?)");
                    $stmt->execute([$userId]);
                    
                    // Log de seguridad
                    $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent) VALUES (?, 'REGISTER', ?, ?)");
                    $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
                    
                    $success = '[ ÉXITO ] Cuenta creada correctamente. Ahora puedes iniciar sesión.';
                    $mode = 'login';
                }
            } catch (PDOException $e) {
                $error = '[ ERROR ] Error al crear la cuenta: ' . $e->getMessage();
            }
        }
        
    } else {
        // LOGIN
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = '[ ACCESO DENEGADO ] Credenciales incompletas';
        } else {
            try {
                $conn = getDBConnection();
                
                // Buscar usuario
                $stmt = $conn->prepare("SELECT id, username, password_hash, master_salt, login_attempts, locked_until FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Verificar si la cuenta está bloqueada
                    if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                        $error = '[ CUENTA BLOQUEADA ] Demasiados intentos fallidos. Intenta más tarde.';
                    } else {
                        // Verificar contraseña
                        if (verifyPassword($password, $user['password_hash'])) {
                            // Login exitoso
                            $_SESSION['user_logged_in'] = true;
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['master_salt'] = $user['master_salt'];
                            $_SESSION['master_key'] = $password; // Guardar temporalmente para encriptación
                            $_SESSION['login_time'] = time();
                            
                            // Resetear intentos de login y actualizar última conexión
                            $stmt = $conn->prepare("UPDATE users SET login_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id = ?");
                            $stmt->execute([$user['id']]);
                            
                            // Log de seguridad
                            $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent) VALUES (?, 'LOGIN', ?, ?)");
                            $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
                            
                            header('Location: index.php');
                            exit();
                        } else {
                            // Contraseña incorrecta - incrementar intentos
                            $attempts = $user['login_attempts'] + 1;
                            
                            if ($attempts >= 5) {
                                // Bloquear cuenta por 15 minutos
                                $stmt = $conn->prepare("UPDATE users SET login_attempts = ?, locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id = ?");
                                $stmt->execute([$attempts, $user['id']]);
                                $error = '[ CUENTA BLOQUEADA ] Demasiados intentos fallidos. Cuenta bloqueada por 15 minutos.';
                            } else {
                                $stmt = $conn->prepare("UPDATE users SET login_attempts = ? WHERE id = ?");
                                $stmt->execute([$attempts, $user['id']]);
                                $remaining = 5 - $attempts;
                                $error = "[ ACCESO DENEGADO ] Contraseña incorrecta. Intentos restantes: $remaining";
                            }
                            
                            // Log de seguridad
                            $stmt = $conn->prepare("INSERT INTO security_logs (user_id, action, ip_address, user_agent, details) VALUES (?, 'FAILED_LOGIN', ?, ?, ?)");
                            $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', "Intento $attempts"]);
                        }
                    }
                } else {
                    $error = '[ ACCESO DENEGADO ] Usuario no encontrado';
                }
            } catch (PDOException $e) {
                $error = '[ ERROR ] Error al procesar login: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Terminal Segura</title>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="matrix-bg" id="matrix"></div>
    <div class="scanlines"></div>

    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; position: relative; z-index: 2;">
        <div class="terminal-box" style="max-width: 600px;">
            <div class="terminal-header">
                <div class="terminal-dot dot-red"></div>
                <div class="terminal-dot dot-yellow"></div>
                <div class="terminal-dot dot-green"></div>
                <div class="terminal-title"><?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?> - TERMINAL SEGURA</div>
            </div>
            <div style="padding: 30px;">
                <div class="logo">
                    <h1>▓▒░ CYBERSHIELD ░▒▓</h1>
                    <p class="subtitle">[ SISTEMA DE SEGURIDAD AVANZADO ]</p>
                </div>

                <?php if ($error): ?>
                <div style="background: rgba(255, 0, 0, 0.2); border: 1px solid #ff0000; padding: 12px; margin-bottom: 20px; border-radius: 3px; color: #ff0000; text-align: center;">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div style="background: rgba(0, 255, 65, 0.2); border: 1px solid #00ff41; padding: 12px; margin-bottom: 20px; border-radius: 3px; color: #00ff41; text-align: center;">
                    <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <!-- Botones de cambio de modo -->
                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <button type="button" class="btn-small" style="flex: 1; <?php echo $mode === 'login' ? 'background: rgba(0, 255, 65, 0.2); border-color: #00ffff; color: #00ffff;' : ''; ?>" onclick="switchMode('login')">
                        [ INICIAR SESIÓN ]
                    </button>
                    <button type="button" class="btn-small" style="flex: 1; <?php echo $mode === 'register' ? 'background: rgba(0, 255, 65, 0.2); border-color: #00ffff; color: #00ffff;' : ''; ?>" onclick="switchMode('register')">
                        [ REGISTRARSE ]
                    </button>
                </div>

                <!-- Formulario de Login -->
                <form method="POST" action="" id="loginForm" style="<?php echo $mode === 'register' ? 'display: none;' : ''; ?>">
                    <input type="hidden" name="mode" value="login">
                    
                    <div class="input-group">
                        <div class="terminal-prompt">USUARIO O EMAIL:</div>
                        <input type="text" name="username" placeholder="usuario@email.com" required autofocus>
                    </div>
                    
                    <div class="input-group">
                        <div class="terminal-prompt">CONTRASEÑA:</div>
                        <input type="password" name="password" placeholder="********" required>
                    </div>
                    
                    <button type="submit" class="btn">>> ACCEDER AL SISTEMA</button>
                </form>

                <!-- Formulario de Registro -->
                <form method="POST" action="" id="registerForm" style="<?php echo $mode === 'login' ? 'display: none;' : ''; ?>">
                    <input type="hidden" name="mode" value="register">
                    
                    <div class="input-group">
                        <div class="terminal-prompt">USUARIO:</div>
                        <input type="text" name="username" placeholder="tu_usuario" required <?php echo $mode === 'register' ? 'autofocus' : ''; ?>>
                    </div>
                    
                    <div class="input-group">
                        <div class="terminal-prompt">EMAIL:</div>
                        <input type="email" name="email" placeholder="email@ejemplo.com" required>
                    </div>
                    
                    <div class="input-group">
                        <div class="terminal-prompt">CONTRASEÑA (mín. 8 caracteres):</div>
                        <input type="password" name="password" placeholder="********" required>
                    </div>
                    
                    <div class="input-group">
                        <div class="terminal-prompt">CONFIRMAR CONTRASEÑA:</div>
                        <input type="password" name="confirm_password" placeholder="********" required>
                    </div>
                    
                    <button type="submit" class="btn">>> CREAR CUENTA</button>
                </form>

                <div style="margin-top: 20px; padding: 15px; background: rgba(0, 255, 255, 0.1); border: 1px solid rgba(0, 255, 255, 0.3); border-radius: 3px;">
                    <p style="color: #00ffff; font-size: 0.8em; line-height: 1.6; text-align: center;">
                        <strong>[ SEGURIDAD IMPLEMENTADA ]</strong><br>
                        ✓ Contraseñas: Argon2id / bcrypt<br>
                        ✓ Datos: Encriptación AES-256-CBC<br>
                        ✓ Claves derivadas: PBKDF2 (100,000 iteraciones)
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        function switchMode(mode) {
            if (mode === 'login') {
                document.getElementById('loginForm').style.display = 'block';
                document.getElementById('registerForm').style.display = 'none';
            } else {
                document.getElementById('loginForm').style.display = 'none';
                document.getElementById('registerForm').style.display = 'block';
            }
        }
    </script>
</body>
</html>