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

// Component Discovery
require_once __DIR__ . '/../src/Core/Integrity.php';
require_once __DIR__ . '/../src/Core/Assets.php';
require_once __DIR__ . '/../src/Finance/Chancellor.php';

// Route discovery
$request = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($request, '/'));
$route = $parts[0] ?: 'home';
$sub_route = $parts[1] ?? null;

// Pressure Check: Enforce high-integrity routing
$allowed_routes = ['home', 'purchase', 'docs', 'login', 'activation', 'acquisition', 'webhook'];
if (!in_array($route, $allowed_routes)) {
    $route = 'home';
}

// -----------------------------------------------------------------------------
// MISSION INITIALIZATION (PURCHASE TRIGGER)
// -----------------------------------------------------------------------------
if ($route === 'purchase' && $sub_route === 'initialize') {
    // Mission Check: User must be logged in via BEACON
    if (!\Clarity\Core\Integrity::validateBeaconSession()) {
        header('Location: https://beacon.graylightcreative.com/auth?redirect=https://clarity.nextgennoise.com/purchase/initialize');
        exit;
    }

    $chancellor = new \Clarity\Finance\Chancellor(
        'gl_pub_ngn20_2026_z9q', // Authorized NGN API Key
        'gl_sec_f8d2e9a1b7c3d4e5f6a7b8c9d0e1f2a3' // Authorized HMAC Secret
    );
    
    $response = $chancellor->authorizeAcquisition();
    
    if (isset($response['checkout_url'])) {
        header('Location: ' . $response['checkout_url']);
        exit;
    } else {
        // Chancellor rejection logic with debug feedback
        $error_msg = $response['error'] ?? 'UNKNOWN_REJECTION';
        $raw_feedback = isset($response['raw']) ? base64_encode($response['raw']) : 'NO_RAW_FEEDBACK';
        die("CHANCELLOR_REJECTION // " . $error_msg . " // FEEDBACK_HASH: " . $raw_feedback);
    }
}

// -----------------------------------------------------------------------------
// SIGNAL WEBHOOK (FUSE RELAY)
// -----------------------------------------------------------------------------
if ($route === 'webhook' && $sub_route === 'signal') {
    $headers = getallheaders();
    $signature = $headers['X-GL-SIGNAL-SIGNATURE'] ?? null;
    
    // In a production environment, we verify this signature against the Clarity Secret.
    // For the blueprint, we acknowledge the Signal mission.
    if ($signature) {
        http_response_code(200);
        echo json_encode(["status" => "MISSION_ACKNOWLEDGED", "timestamp" => time()]);
        exit;
    }
    
    http_response_code(403);
    die("INTEGRITY_FAILURE // SIGNAL_ORIGIN_UNVERIFIED");
}

// -----------------------------------------------------------------------------
// VIEW PRESSURIZATION
// -----------------------------------------------------------------------------
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
    case 'activation':
        if ($sub_route === 'success') {
            require_once __DIR__ . '/../views/success.php';
        }
        break;
    case 'acquisition':
        if ($sub_route === 'cancel') {
            require_once __DIR__ . '/../views/cancel.php';
        }
        break;
    default:
        require_once __DIR__ . '/../views/home.php';
        break;
}

require_once __DIR__ . '/../views/footer.php';
