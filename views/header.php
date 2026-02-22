<!DOCTYPE html>
<html lang="en" class="bg-[#0A0A0A] text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'CLARITY NGN // THE MIXING MENTOR'; ?></title>
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
                @apply bg-ngn-orange text-ngn-charcoal px-6 py-3 font-bold uppercase tracking-tighter transition-all duration-200 hover:shadow-[0_0_20px_rgba(255,95,31,0.4)] hover:scale-[1.02] active:scale-95 text-center;
            }
            .nav-link {
                @apply text-xs uppercase tracking-widest text-white/60 hover:text-ngn-orange transition-colors;
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

    <header class="border-b border-ngn-orange/20 bg-ngn-charcoal/50 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="/" class="flex items-center gap-4 group">
                    <img src="https://nextgennoise.com/lib/images/site/2026/default-avatar.png" alt="NGN" class="w-8 h-8 opacity-80 group-hover:opacity-100 transition-opacity">
                    <span class="text-2xl font-sans font-bold tracking-tighter text-ngn-orange">CLARITY<span class="text-white/20 ml-2 font-light">NGN</span></span>
                </a>
                <nav class="hidden md:flex gap-6 ml-4">
                    <a href="/docs" class="nav-link <?php echo $requestUri === '/docs' ? 'text-ngn-orange' : ''; ?>">Documentation</a>
                    <a href="/purchase" class="nav-link <?php echo $requestUri === '/purchase' ? 'text-ngn-orange' : ''; ?>">Purchase</a>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <a href="/login" class="text-[10px] uppercase tracking-widest text-white/40 hover:text-white transition-colors">Beacon Login</a>
                <a href="/purchase" class="btn-ngn text-[10px] py-2 px-4">Initialize License</a>
            </div>
        </div>
    </header>
