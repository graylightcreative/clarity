<?php
/**
 * CLARITY NGN // SOVEREIGN GATEWAY
 * Status: PRESSURIZED // ONLINE
 * Theme: NEXTGEN NOISE // FOUNDRY DNA
 */

require_once __DIR__ . '/../src/Core/Integrity.php';
use Clarity\Core\Integrity;

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="en" class="bg-[#0A0A0A] text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLARITY NGN // THE MIXING MENTOR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Space+Grotesk:wght@300;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ngn-orange': '#FF5F1F',
                        'ngn-charcoal': '#0A0A0A',
                    },
                    fontFamily: {
                        mono: ['JetBrains Mono', 'monospace'],
                        sans: ['Space Grotesk', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply font-mono antialiased;
                background-image: radial-gradient(circle at 2px 2px, rgba(255, 95, 31, 0.05) 1px, transparent 0);
                background-size: 40px 40px;
            }
        }
        @layer components {
            .sp-card {
                @apply bg-[#0A0A0A]/80 border border-ngn-orange/20 rounded-lg p-8 backdrop-blur-xl shadow-2xl;
            }
            .btn-ngn {
                @apply bg-ngn-orange text-ngn-charcoal px-6 py-3 font-bold uppercase tracking-tighter transition-all duration-200 hover:shadow-[0_0_20px_rgba(255,95,31,0.4)] hover:scale-[1.02] active:scale-95;
            }
            .nav-link {
                @apply text-sm uppercase tracking-widest text-white/60 hover:text-ngn-orange transition-colors;
            }
            .grid-overlay {
                position: fixed;
                inset: 0;
                background-image: linear-gradient(to right, rgba(255,95,31,0.03) 1px, transparent 1px),
                                  linear-gradient(to bottom, rgba(255,95,31,0.03) 1px, transparent 1px);
                background-size: 100px 100px;
                pointer-events: none;
                z-index: -1;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <div class="grid-overlay"></div>

    <!-- Header / Navigation -->
    <header class="border-b border-ngn-orange/20 bg-ngn-charcoal/50 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <span class="text-2xl font-sans font-bold tracking-tighter text-ngn-orange">CLARITY<span class="text-white/20 ml-2 font-light">NGN</span></span>
                <nav class="hidden md:flex gap-6">
                    <a href="#" class="nav-link text-ngn-orange">Portal</a>
                    <a href="#" class="nav-link">Documentation</a>
                    <a href="#" class="nav-link">Fleet Status</a>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <a href="/login" class="text-xs uppercase tracking-widest text-white/40 hover:text-white transition-colors">Beacon Login</a>
                <a href="/purchase" class="btn-ngn text-sm">Get License</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow max-w-7xl mx-auto px-6 py-20 w-full">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-block px-3 py-1 bg-ngn-orange/10 border border-ngn-orange/30 text-ngn-orange text-[10px] uppercase tracking-[0.2em] mb-6">
                    Sovereign Deployment // v1.0.0
                </div>
                <h1 class="text-6xl md:text-8xl font-sans font-bold tracking-tighter leading-none mb-8">
                    THE MIXING <br> <span class="text-ngn-orange">MENTOR.</span>
                </h1>
                <p class="text-xl text-white/60 leading-relaxed mb-10 max-w-lg">
                    High-integrity audio analysis for the modern engineer. Pressurized AI inference meets the Sovereign Rig.
                </p>
                <div class="flex gap-4">
                    <a href="/purchase" class="btn-ngn">Initialize Acquisition</a>
                    <a href="#" class="px-6 py-3 border border-white/10 text-sm uppercase font-bold hover:bg-white/5 transition-colors">Download Trial</a>
                </div>
            </div>

            <!-- Dashboard Mockup -->
            <div class="sp-card relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4">
                    <div class="w-2 h-2 rounded-full bg-ngn-orange animate-pulse"></div>
                </div>
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded bg-ngn-orange/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-ngn-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Fleet Telemetry</h3>
                        <p class="text-xs text-white/40 uppercase tracking-widest">Real-time Node Status</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-white/5 p-4 rounded flex justify-between items-center border border-white/5">
                        <span class="text-sm">Rig Identity</span>
                        <span class="text-sm font-bold text-ngn-orange">BEACON_ACTIVE</span>
                    </div>
                    <div class="bg-white/5 p-4 rounded flex justify-between items-center border border-white/5">
                        <span class="text-sm">Revenue Split</span>
                        <span class="text-sm font-bold text-ngn-orange">90/10_ENFORCED</span>
                    </div>
                    <div class="bg-white/5 p-4 rounded flex justify-between items-center border border-white/5">
                        <span class="text-sm">Inference Engine</span>
                        <span class="text-sm font-bold">PHP_8.5.1_STABLE</span>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-white/5">
                    <div class="flex justify-between text-[10px] uppercase tracking-widest text-white/20">
                        <span>Terminal Velocity Deployment</span>
                        <span>0% Packet Loss</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/5 py-12 bg-black/20">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="text-[10px] uppercase tracking-[0.4em] text-white/20">
                &copy; 2026 Graylight Creative // Sovereign Rig Standards
            </div>
            <div class="flex gap-8">
                <a href="#" class="text-[10px] uppercase tracking-widest text-white/40 hover:text-ngn-orange transition-colors">Privacy Protocol</a>
                <a href="#" class="text-[10px] uppercase tracking-widest text-white/40 hover:text-ngn-orange transition-colors">Terms of Sovereignty</a>
            </div>
        </div>
    </footer>
</body>
</html>
