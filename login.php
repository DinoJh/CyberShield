<?php
// login.php - Página de inicio de sesión
require_once 'config.php';

// Si ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $masterPassword = $_POST['master_password'] ?? '';
    
    // Validación simple (por ahora usa la contraseña por defecto)
    if ($masterPassword === DEFAULT_PASSWORD || strlen($masterPassword) >= 6) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['master_key'] = $masterPassword;
        $_SESSION['user_id'] = 1; // Por ahora usuario fijo
        $_SESSION['login_time'] = time();
        
        header('Location: index.php');
        exit();
    } else {
        $error = '[ ACCESO DENEGADO ] Credenciales inválidas o clave muy corta';
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
        <div class="terminal-box">
            <div class="terminal-header">
                <div class="terminal-dot dot-red"></div>
                <div class="terminal-dot dot-yellow"></div>
                <div class="terminal-dot dot-green"></div>
                <div class="terminal-title"><?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?> - TERMINAL SEGURA</div>
            </div>
            <div style="padding: 30px;">
                <div class="logo">
                    <h1>▓▒░ CYBERSHIELD ░▒▓</h1>
                    <p class="subtitle">[ ACCESO AUTORIZADO ÚNICAMENTE ]</p>
                </div>

                <?php if (isset($error)): ?>
                <div style="background: rgba(255, 0, 0, 0.2); border: 1px solid #ff0000; padding: 12px; margin-bottom: 20px; border-radius: 3px; color: #ff0000; text-align: center;">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="input-group">
                        <div class="terminal-prompt">INGRESE CLAVE MAESTRA:</div>
                        <input type="password" name="master_password" id="masterPassword" placeholder="********" required autofocus>
                    </div>
                    <button type="submit" class="btn">>> INICIALIZAR SISTEMA</button>
                </form>

                <p class="hint">
                    CLAVE POR DEFECTO: <strong><?php echo DEFAULT_PASSWORD; ?></strong>
                </p>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Enter key para submit
        document.getElementById('masterPassword').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    </script>
</body>
</html>