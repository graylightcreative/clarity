<?php
/**
 * CLARITY NGN // SOVEREIGN GATEWAY
 * Status: PRESSURIZED // ONLINE
 * Theme: NEXTGEN NOISE // FOUNDRY DNA
 */

require_once __DIR__ . '/../src/Core/Integrity.php';
use Clarity\Core\Integrity;

// 1. Simple Router logic
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 2. Page Metadata
$pageTitle = 'CLARITY NGN // THE MIXING MENTOR';
$view = 'home.php';

switch ($requestUri) {
    case '/':
        $view = 'home.php';
        break;
    case '/purchase':
        $pageTitle = 'CLARITY NGN // INITIALIZE LICENSE';
        $view = 'purchase.php';
        break;
    case '/docs':
        $pageTitle = 'CLARITY NGN // INTEGRATION PROTOCOLS';
        $view = 'docs.php';
        break;
    case '/login':
        // For now, redirect or show placeholder
        header('Location: https://beacon.graylightcreative.com/auth');
        exit;
    default:
        $view = 'home.php';
}

// 3. Load UI
require_once __DIR__ . '/../views/header.php';
require_once __DIR__ . '/../views/' . $view;
require_once __DIR__ . '/../views/footer.php';
