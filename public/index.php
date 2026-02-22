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
</head>
<body>
    <header>
        <div class="logo">CLARITY NGN</div>
        <nav>
            <a href="/" class="btn-primary" style="padding: 0.5rem 1rem;">Dashboard</a>
            <a href="/purchase" class="btn-primary" style="padding: 0.5rem 1rem; margin-left: 1rem;">Purchase</a>
        </nav>
    </header>

    <main style="padding: 4rem 10%;">
        <div class="sp-card">
            <h1>Sovereign Status: ONLINE</h1>
            <p style="color: var(--text-secondary);">Welcome to the Graylight Foundry. The Rig is pressurized at 209.59.156.82.</p>
            
            <div style="margin-top: 2rem; border-top: 1px solid var(--glass-border); padding-top: 2rem;">
                <h3>FLEET TELEMETRY</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.5rem;"><span style="color: var(--electric-orange);">Runtime:</span> PHP 8.5.1</li>
                    <li style="margin-bottom: 0.5rem;"><span style="color: var(--electric-orange);">Database:</span> mysql.starrship1.com (MariaDB)</li>
                    <li style="margin-bottom: 0.5rem;"><span style="color: var(--electric-orange);">Identity:</span> auth.starrship1.com (Beacon SSO)</li>
                </ul>
            </div>
        </div>
    </main>

    <footer style="text-align: center; padding: 2rem; color: var(--text-secondary); font-size: 0.8rem;">
        &copy; 2026 GRAYLIGHT CREATIVE // SOVEREIGN RIG STANDARDS
    </footer>
</body>
</html>
