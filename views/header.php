<!DOCTYPE html>
<html lang="en" class="bg-[#0A0A0A] text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>CLARITY NGN // <?php echo strtoupper($route ?: 'THE MIXING MENTOR'); ?></title>
    <link rel="icon" type="image/png" href="https://nextgennoise.com/lib/images/site/2026/default-avatar.png">
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
                    },
                    animation: {
                        'pulse-fast': 'pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'glitch': 'glitch 1s linear infinite',
                        'scanline': 'scanline 8s linear infinite',
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply font-mono antialiased overflow-x-hidden;
                background-color: #0A0A0A;
                background-image: 
                    radial-gradient(circle at 2px 2px, rgba(255, 95, 31, 0.05) 1px, transparent 0),
                    linear-gradient(to right, rgba(255, 95, 31, 0.02) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255, 95, 31, 0.02) 1px, transparent 1px);
                background-size: 40px 40px, 100px 100px, 100px 100px;
                /* Mobile Padding for Bottom Nav */
                padding-bottom: 80px;
            }
            @media (min-width: 1024px) {
                body { padding-bottom: 0; }
            }
            /* Scanline Overlay */
            body::after {
                content: "";
                position: fixed;
                top: 0; left: 0; width: 100%; height: 100%;
                background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%),
                            linear-gradient(90deg, rgba(255, 0, 0, 0.02), rgba(0, 255, 0, 0.01), rgba(0, 0, 255, 0.02));
                background-size: 100% 4px, 3px 100%;
                pointer-events: none;
                z-index: 100;
                opacity: 0.3;
            }
        }
        @layer components {
            .sp-card {
                @apply bg-[#0A0A0A]/90 border border-ngn-orange/20 rounded-none p-6 md:p-8 backdrop-blur-xl shadow-[0_0_30px_rgba(0,0,0,0.5)] relative;
                clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), calc(100% - 20px) 100%, 0 100%);
            }
            .btn-ngn {
                @apply bg-ngn-orange text-ngn-charcoal px-6 py-4 md:py-3 font-bold uppercase tracking-tighter transition-all duration-200 hover:shadow-[0_0_30px_rgba(255,95,31,0.6)] active:scale-95 text-center relative overflow-hidden flex items-center justify-center;
                clip-path: polygon(10% 0, 100% 0, 100% 70%, 90% 100%, 0 100%, 0 30%);
            }
            .nav-link {
                @apply text-[10px] uppercase tracking-[0.3em] text-white/40 hover:text-ngn-orange transition-colors flex items-center gap-2;
            }
            .hud-border {
                position: relative;
            }
            .hud-border::before {
                content: "";
                position: absolute;
                top: -2px; left: -2px; right: -2px; bottom: -2px;
                border: 1px solid rgba(255, 95, 31, 0.3);
                pointer-events: none;
            }
            .mobile-hud-btn {
                @apply flex flex-col items-center justify-center gap-1 w-full h-full text-white/40;
            }
            .mobile-hud-btn.active {
                @apply text-ngn-orange font-bold;
            }
        }

        @keyframes scanline {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Animated Scanline -->
    <div class="fixed inset-0 pointer-events-none z-[101] opacity-[0.03] overflow-hidden">
        <div class="w-full h-20 bg-gradient-to-b from-transparent via-ngn-orange to-transparent animate-scanline"></div>
    </div>

    <!-- Desktop Header -->
    <header class="border-b border-ngn-orange/30 bg-ngn-charcoal/80 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 md:h-24 flex items-center justify-between">
            <div class="flex items-center gap-12">
                <a href="/" class="flex items-center gap-3 md:gap-4 group">
                    <!-- Audio Reticle SVG -->
                    <svg width="32" height="32" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="group-hover:rotate-90 transition-transform duration-500 md:w-10 md:h-10">
                        <circle cx="20" cy="20" r="18" stroke="#FF5F1F" stroke-width="0.5" stroke-dasharray="4 4"/>
                        <path d="M20 5V10M20 30V35M5 20H10M30 20H35" stroke="#FF5F1F" stroke-width="1"/>
                        <rect x="18" y="18" width="4" height="4" fill="#FF5F1F" class="animate-pulse"/>
                        <path d="M12 20C12 15.5817 15.5817 12 20 12" stroke="#FF5F1F" stroke-width="0.5"/>
                    </svg>
                    <div class="flex flex-col">
                        <span class="text-xl md:text-2xl font-sans font-bold tracking-tighter text-ngn-orange leading-none">CLARITY<span class="text-white ml-1">NGN</span></span>
                        <span class="text-[7px] md:text-[8px] uppercase tracking-[0.5em] text-white/20 font-mono mt-1 italic">HUD // v1.0.4</span>
                    </div>
                </a>
                
                <nav class="hidden lg:flex gap-10 items-center">
                    <a href="/docs" class="nav-link <?php echo $route == 'docs' ? 'text-ngn-orange' : ''; ?>">
                        <span class="w-1.5 h-1.5 rounded-full border border-ngn-orange/40"></span>
                        Knowledge_DB
                    </a>
                    <a href="/purchase" class="nav-link <?php echo $route == 'purchase' ? 'text-ngn-orange' : ''; ?>">
                        <span class="w-1.5 h-1.5 rounded-full border border-ngn-orange/40"></span>
                        Acquisition_Core
                    </a>
                </nav>
            </div>

            <div class="flex items-center gap-4 md:gap-8 font-mono">
                <div class="hidden md:flex flex-col items-end">
                    <div class="flex items-center gap-2">
                        <span class="text-[8px] uppercase tracking-widest text-white/20">Fleet_Sync</span>
                        <span class="text-[10px] text-ngn-orange animate-pulse">PRESSURIZED</span>
                    </div>
                    <div class="text-[10px] text-white/10 uppercase tracking-[0.2em]">Ping: 12ms</div>
                </div>
                <div class="hidden md:block h-10 w-px bg-white/10"></div>
                <a href="/purchase" class="btn-ngn text-[9px] md:text-[10px] py-2 md:py-3 px-4 md:px-8 group">
                    <span class="relative z-10">INITIALIZE_MISSION</span>
                </a>
            </div>
        </div>
        
        <!-- Tech Sub-header (Desktop Only) -->
        <div class="hidden md:flex bg-ngn-orange/5 border-t border-ngn-orange/10 h-6 items-center">
            <div class="max-w-7xl mx-auto px-6 w-full flex justify-between text-[7px] uppercase tracking-[0.4em] text-ngn-orange/40 font-bold italic">
                <span>// Neural_Engine: Online</span>
                <span>// Inference_Target: Set</span>
                <span>// Session_Integrity: Verified</span>
            </div>
        </div>
    </header>

    <!-- Mobile Bottom HUD Navigation -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-[110] bg-ngn-charcoal border-t border-ngn-orange/30 backdrop-blur-2xl">
        <div class="grid grid-cols-4 h-20 items-center">
            <a href="/" class="mobile-hud-btn <?php echo $route == 'home' ? 'active' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                <span class="text-[8px] uppercase tracking-widest">Home</span>
            </a>
            <a href="/docs" class="mobile-hud-btn <?php echo $route == 'docs' ? 'active' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                <span class="text-[8px] uppercase tracking-widest">Docs</span>
            </a>
            <a href="/purchase" class="mobile-hud-btn <?php echo $route == 'purchase' ? 'active' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                </svg>
                <span class="text-[8px] uppercase tracking-widest">Buy</span>
            </a>
            <a href="/login" class="mobile-hud-btn <?php echo $route == 'login' ? 'active' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span class="text-[8px] uppercase tracking-widest">User</span>
            </a>
        </div>
        <!-- Decorative HUD Corner -->
        <div class="absolute -top-1 -right-1 w-4 h-4 border-t border-r border-ngn-orange/50"></div>
        <div class="absolute -top-1 -left-1 w-4 h-4 border-t border-l border-ngn-orange/50"></div>
    </nav>
