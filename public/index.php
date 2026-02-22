<?php
/**
 * CLARITY NGN // SOVEREIGN GATEWAY
 * High-Integrity Vanilla PHP Router
 */

// Critical Rig Recovery: Handle internal loopback routing
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path !== '/' && file_exists(__DIR__ . $path)) {
        return false;
    }
}

// Route discovery
$request = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($request, '/'));
$route = $parts[0] ?: 'home';

// Pressure Check: Enforce high-integrity routing
$allowed_routes = ['home', 'purchase', 'docs', 'login'];
if (!in_array($route, $allowed_routes)) {
    $route = 'home';
}

// Component Assembly
require_once __DIR__ . '/../src/Core/Integrity.php';

// Pressurize Views
require_once __DIR__ . '/../views/header.php';

switch ($route) {
    case 'home':
        require_once __DIR__ . '/../views/home.php';
        break;
    case 'purchase':
        require_once __DIR__ . '/../views/purchase.php';
        break;
    case 'docs':
        require_once __DIR__ . '/../views/docs.php';
        break;
    default:
        require_once __DIR__ . '/../views/home.php';
        break;
}

require_once __DIR__ . '/../views/footer.php';
