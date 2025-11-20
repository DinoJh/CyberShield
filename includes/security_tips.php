<?php
// includes/security_tips.php - Avisos de seguridad diarios personalizados

$securityTips = [
    [
        'title' => 'PON A PRUEBA TUS CONTRASEÑAS CON NUESTRO SISTEMA DE SEGURIDAD',
        'description' => 'Deja que nos encarguemos de verificar que tus contraseñas sean seguras para prevenir posibles ataques de hacking como los de fuerza bruta, phishing y otros vectores de ataque comunes. Nuestro analizador utiliza algoritmos avanzados para evaluar la robustez de tus credenciales en tiempo real.'
    ],
    [
        'title' => 'ACTIVA LA AUTENTICACIÓN DE DOS FACTORES EN TODAS TUS CUENTAS',
        'description' => 'La autenticación de dos factores (2FA) añade una capa adicional de seguridad que hace que sea extremadamente difícil para los atacantes acceder a tus cuentas, incluso si obtienen tu contraseña. Es tu mejor defensa contra el robo de identidad y accesos no autorizados.'
    ],
    [
        'title' => 'ACTUALIZA TUS CONTRASEÑAS CADA 90 DÍAS',
        'description' => 'Cambiar regularmente tus contraseñas reduce el riesgo de que credenciales comprometidas sean utilizadas en tu contra. Esta práctica es especialmente importante para cuentas sensibles como correo electrónico, banca online y redes sociales. CyberShield re-encripta automáticamente todos tus datos al cambiar tu contraseña.'
    ],
    [
        'title' => 'NUNCA REUTILICES LA MISMA CONTRASEÑA EN DIFERENTES SERVICIOS',
        'description' => 'Si un sitio web es comprometido y filtran tu contraseña, los atacantes intentarán usar esas mismas credenciales en otros servicios. Usar contraseñas únicas para cada cuenta asegura que una brecha de seguridad en un servicio no comprometa todas tus cuentas.'
    ],
    [
        'title' => 'VERIFICA SIEMPRE LOS CERTIFICADOS SSL ANTES DE INGRESAR DATOS',
        'description' => 'Asegúrate de que el sitio web use HTTPS (candado en la barra de direcciones) antes de ingresar información sensible. Los certificados SSL encriptan la comunicación entre tu navegador y el servidor, protegiendo tus datos de interceptaciones maliciosas.'
    ],
    [
        'title' => 'MANTÉN TU SISTEMA Y SOFTWARE SIEMPRE ACTUALIZADOS',
        'description' => 'Las actualizaciones de software no solo traen nuevas funciones, también parchean vulnerabilidades de seguridad críticas. Los ciberdelincuentes explotan activamente estas vulnerabilidades conocidas, por lo que mantener todo actualizado es esencial para tu protección.'
    ],
    [
        'title' => 'USA UN GESTOR DE CONTRASEÑAS PARA ALMACENAMIENTO SEGURO',
        'description' => 'CyberShield actúa como tu gestor de contraseñas personal, almacenando tus credenciales con encriptación AES-256 de grado militar. Esto te permite usar contraseñas complejas y únicas sin tener que recordarlas todas, mejorando significativamente tu seguridad.'
    ],
    [
        'title' => 'MONITOREA LOS REGISTROS DE ACTIVIDAD DE TUS CUENTAS',
        'description' => 'Revisa regularmente los logs de inicio de sesión y actividades sospechosas en tus cuentas importantes. La detección temprana de accesos no autorizados puede prevenir daños mayores. CyberShield registra todas tus acciones para que puedas auditar tu actividad.'
    ],
    [
        'title' => 'RESPALDA TUS DATOS ENCRIPTADOS EN UBICACIONES SEGURAS',
        'description' => 'Aunque tus datos en CyberShield están protegidos con encriptación de grado militar, es importante mantener copias de seguridad de información crítica en dispositivos sin conexión. Esto te protege contra pérdida de datos por fallas de hardware o ataques de ransomware.'
    ],
    [
        'title' => 'EVITA CONECTARTE A REDES WIFI PÚBLICAS PARA TRANSACCIONES SENSIBLES',
        'description' => 'Las redes WiFi públicas son el objetivo perfecto para ataques "man-in-the-middle" donde los atacantes pueden interceptar tu tráfico. Si debes usar WiFi público, utiliza siempre una VPN confiable y evita acceder a banca online o ingresar contraseñas.'
    ],
    [
        'title' => 'APRENDE A IDENTIFICAR CORREOS DE PHISHING Y SITIOS FRAUDULENTOS',
        'description' => 'Los ataques de phishing son cada vez más sofisticados. Desconfía de correos urgentes, revisa cuidadosamente las URL, verifica la ortografía y nunca hagas clic en enlaces sospechosos. Los criminales usan ingeniería social para manipularte y robar tus credenciales.'
    ],
    [
        'title' => 'ENCRIPTA TUS COMUNICACIONES SENSIBLES SIEMPRE QUE SEA POSIBLE',
        'description' => 'Usa el módulo de encriptación de mensajes de CyberShield para proteger información confidencial antes de compartirla. La encriptación de extremo a extremo asegura que solo tú y el destinatario autorizado puedan leer el contenido del mensaje.'
    ],
    [
        'title' => 'CONFIGURA UNA CONTRASEÑA ADICIONAL PARA TU BÓVEDA',
        'description' => 'La doble capa de protección de CyberShield añade seguridad extra a tus datos más sensibles. Incluso si alguien obtiene acceso a tu cuenta, no podrá ver el contenido de tu bóveda sin la contraseña adicional. Es como tener una caja fuerte dentro de otra.'
    ],
    [
        'title' => 'REVISA Y ELIMINA ACCESOS Y PERMISOS DE APLICACIONES ANTIGUAS',
        'description' => 'Periódicamente revisa qué aplicaciones y servicios tienen acceso a tus cuentas. Elimina permisos de apps que ya no uses. Los tokens de acceso antiguos o servicios desactualizados pueden convertirse en puertas traseras para atacantes.'
    ],
    [
        'title' => 'UTILIZA CONTRASEÑAS DE AL MENOS 16 CARACTERES CON ALTA COMPLEJIDAD',
        'description' => 'Las contraseñas largas y complejas son exponencialmente más difíciles de descifrar mediante ataques de fuerza bruta. Usa nuestro generador para crear contraseñas con mayúsculas, minúsculas, números y símbolos especiales que sean virtualmente imposibles de adivinar.'
    ]
];

// Función para obtener el tip del día basado en la fecha
function getDailySecurityTip() {
    global $securityTips;
    
    // Usar el día del año para rotar los tips
    $dayOfYear = date('z'); // 0-365
    $tipIndex = $dayOfYear % count($securityTips);
    
    return $securityTips[$tipIndex];
}

// Función para obtener un tip aleatorio
function getRandomSecurityTip() {
    global $securityTips;
    return $securityTips[array_rand($securityTips)];
}
?>