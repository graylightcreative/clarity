<?php

/**
 * CLARITY NGN // SOVEREIGN GATEWAY
 * Status: PRESSURIZED // ONLINE
 */

require_once __DIR__ . '/../src/Core/Integrity.php';

use Clarity\Core\Integrity;

// 1. Initialize Rig Identity
$rigApiKey = $_ENV['CLARITY_API_KEY'] ?? 'CLARITY_V1_SOVEREIGN_KEY';
$rigSecret = $_ENV['CLARITY_SECRET_KEY'] ?? 'CLARITY_V1_SOVEREIGN_SECRET';

// 2. Perform Sovereign Integrity Handshake (if API request)
$isApiRequest = str_starts_with($_SERVER['REQUEST_URI'], '/api/');

if ($isApiRequest) {
    if (!Integrity::validateHandshake($rigApiKey, $rigSecret)) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'SOVEREIGN_INTEGRITY_FAIL', 'status' => 'PRESSURIZED_REJECTION']);
        exit;
    }
}

// 3. Simple Router logic
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 4. Load Layout and View
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLARITY NGN // SOVEREIGN HUB</title>
    <link rel="stylesheet" href="/assets/css/foundry.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Space+Grotesk:wght@300;700&display=swap');
    </style>
</head>
<body>
    <header>
        <div class="logo">CLARITY NGN</div>
        <nav>
            <a href="/" class="btn-primary" style="padding: 0.5rem 1rem; text-decoration: none;">Dashboard</a>
            <a href="/purchase" class="btn-primary" style="padding: 0.5rem 1rem; margin-left: 1rem; text-decoration: none;">Purchase</a>
        </nav>
    </header>

    <main style="padding: 4rem 10%;">
        <div class="sp-card">
            <h1 style="font-family: var(--font-sans); font-weight: 300; letter-spacing: -2px; font-size: 3rem; margin-bottom: 1rem;">Sovereign Status: <span style="color: var(--electric-orange);">ONLINE</span></h1>
            <p style="color: var(--text-secondary); font-size: 1.2rem; max-width: 600px;">Welcome to the Graylight Foundry. The Clarity NGN Hub is now a fully pressurized, independent node in the Sovereign Fleet.</p>
            
            <div style="margin-top: 3rem; border-top: 1px solid var(--glass-border); padding-top: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
                <div>
                    <h3 style="color: var(--electric-orange); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px;">Core Engine</h3>
                    <p style="font-size: 0.9rem;">PHP 8.5.1 (High-Integrity)</p>
                </div>
                <div>
                    <h3 style="color: var(--electric-orange); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px;">Identity</h3>
                    <p style="font-size: 0.9rem;">Beacon SSO (Active)</p>
                </div>
                <div>
                    <h3 style="color: var(--electric-orange); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px;">Ledger</h3>
                    <p style="font-size: 0.9rem;">90/10 Split (Enforced)</p>
                </div>
            </div>

            <div style="margin-top: 3rem; padding: 1.5rem; background: rgba(255, 95, 31, 0.05); border-left: 4px solid var(--electric-orange);">
                <code style="color: var(--electric-orange); font-size: 0.8rem;">TELEMETRY_LANDMARK: 1.0.0-PRESSURIZED</code>
            </div>
        </div>
    </main>

    <footer style="text-align: center; padding: 4rem 2rem; color: var(--text-secondary); font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">
        &copy; 2026 GRAYLIGHT CREATIVE // SOVEREIGN RIG STANDARDS // ZERO FLUFF
    </footer>
</body>
</html>
