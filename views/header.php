<!DOCTYPE html>
<html lang="en" class="bg-[#050505] text-white">
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
                        'ngn-blue': '#00D2FF',
                        'ngn-purple': '#9D50BB',
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
                @apply font-mono antialiased overflow-x-hidden;
                background-color: #050505;
                background-image: 
                    radial-gradient(circle at 50% 50%, rgba(0, 210, 255, 0.03) 0%, transparent 50%),
                    linear-gradient(to right, rgba(255, 255, 255, 0.01) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255, 255, 255, 0.01) 1px, transparent 1px);
                background-size: 100% 100%, 40px 40px, 40px 40px;
                padding-bottom: 80px;
            }
            @media (min-width: 1024px) {
                body { padding-bottom: 0; }
            }
        }
        @layer components {
            .sp-card {
                @apply bg-[#0A0A0A] border border-white/5 rounded-none p-6 md:p-8 backdrop-blur-xl relative;
                background-image: linear-gradient(145deg, rgba(255,255,255,0.02) 0%, transparent 100%);
                box-shadow: 0 20px 50px rgba(0,0,0,0.5), inset 0 1px 1px rgba(255,255,255,0.05);
            }
            .btn-glow-blue {
                @apply bg-ngn-blue/10 border border-ngn-blue/50 text-ngn-blue px-6 py-4 font-bold uppercase tracking-widest transition-all duration-300 hover:bg-ngn-blue hover:text-black hover:shadow-[0_0_30px_rgba(0,210,255,0.5)];
            }
            .nav-link {
                @apply text-[10px] uppercase tracking-[0.3em] text-white/40 hover:text-ngn-blue transition-all duration-300;
            }
            /* Neon SVG Utility (Transforms Black SVGs to Neon) */
            .icon-neon-blue {
                @apply text-ngn-blue;
                filter: drop-shadow(0 0 8px rgba(0, 210, 255, 0.6));
            }
            .icon-neon-blue path, .icon-neon-blue g {
                fill: currentColor !important;
            }
            .icon-neon-purple {
                @apply text-ngn-purple;
                filter: drop-shadow(0 0 8px rgba(157, 80, 187, 0.6));
            }
            .icon-neon-purple path, .icon-neon-purple g {
                fill: currentColor !important;
            }
            .vst-panel {
                @apply border-t-2 border-ngn-blue/50 bg-[#111] relative overflow-hidden;
                box-shadow: 0 -10px 30px rgba(0,210,255,0.1);
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="border-b border-white/5 bg-black/80 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 md:h-24 flex items-center justify-between">
            <div class="flex items-center gap-12">
                <a href="/" class="flex items-center gap-4 group">
                    <div class="relative">
                        <div class="absolute -inset-2 bg-ngn-blue/20 blur-lg rounded-full group-hover:bg-ngn-blue/40 transition-all"></div>
                        <img src="https://nextgennoise.com/lib/images/site/2026/default-avatar.png" alt="NGN" class="w-8 h-8 relative brightness-125">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl md:text-2xl font-sans font-bold tracking-tighter text-white leading-none">NGN <span class="text-ngn-blue">CLARITY</span></span>
                        <span class="text-[7px] uppercase tracking-[0.5em] text-white/20 font-mono mt-1 italic">Professional Mixing Assistant</span>
                    </div>
                </a>
                
                <nav class="hidden lg:flex gap-10 items-center">
                    <a href="/docs" class="nav-link <?php echo $route == 'docs' ? 'text-ngn-blue' : ''; ?>">Features</a>
                    <a href="/purchase" class="nav-link <?php echo $route == 'purchase' ? 'text-ngn-blue' : ''; ?>">Pricing</a>
                    <a href="/docs" class="nav-link">About</a>
                </nav>
            </div>

            <div class="flex items-center gap-4 md:gap-8 font-mono">
                <a href="/login" class="nav-link hidden md:block">User_Login</a>
                <a href="/purchase" class="btn-glow-blue text-[9px] md:text-[10px] py-2 md:py-3 px-4 md:px-8">
                    GET STARTED FREE
                </a>
            </div>
        </div>
    </header>

    <!-- Mobile Bottom HUD -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-[110] bg-black/90 border-t border-white/10 backdrop-blur-2xl">
        <div class="grid grid-cols-4 h-20 items-center">
            <a href="/" class="flex flex-col items-center gap-1 <?php echo $route == 'home' ? 'text-ngn-blue' : 'text-white/40'; ?>">
                <span class="text-[8px] uppercase tracking-widest">Home</span>
            </a>
            <a href="/docs" class="flex flex-col items-center gap-1 <?php echo $route == 'docs' ? 'text-ngn-blue' : 'text-white/40'; ?>">
                <span class="text-[8px] uppercase tracking-widest">Docs</span>
            </a>
            <a href="/purchase" class="flex flex-col items-center gap-1 <?php echo $route == 'purchase' ? 'text-ngn-blue' : 'text-white/40'; ?>">
                <span class="text-[8px] uppercase tracking-widest">Buy</span>
            </a>
            <a href="/login" class="flex flex-col items-center gap-1 <?php echo $route == 'login' ? 'text-ngn-blue' : 'text-white/40'; ?>">
                <span class="text-[8px] uppercase tracking-widest">Account</span>
            </a>
        </div>
    </nav>
