<?php use Clarity\Core\Assets; ?>
<main class="flex-grow w-full relative overflow-hidden">
    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-6 py-24 md:py-40 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-10">
                <div class="inline-flex items-center gap-3 px-4 py-1 bg-ngn-blue/10 border border-ngn-blue/30 text-ngn-blue text-[10px] uppercase tracking-[0.4em] font-bold italic hud-border">
                    <span class="w-2 h-2 rounded bg-ngn-blue animate-pulse"></span>
                    Neural_Engine_Active // v1.0.4
                </div>
                
                <h1 class="text-6xl sm:text-8xl md:text-[120px] font-sans font-bold tracking-tighter leading-[0.8] mb-10 uppercase">
                    ACHIEVE <br> MIXING <br> <span class="text-ngn-blue">CLARITY.</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-white/60 leading-relaxed font-light max-w-xl font-sans tracking-tight">
                    AI-Powered Mixing Assistant for Musicians & Producers. <br>
                    <span class="text-white/20 italic font-mono uppercase tracking-widest text-xs mt-4 block">Analyzing over 50,000 professional stems.</span>
                </p>

                <div class="flex flex-col sm:flex-row gap-6 pt-6">
                    <a href="/purchase" class="btn-glow-blue text-lg px-12 py-6">
                        GET STARTED FREE
                    </a>
                    <a href="/docs" class="nav-link flex items-center justify-center border border-white/10 px-8 py-6">
                        View_Protocols
                    </a>
                </div>
            </div>

            <!-- VST HUD Preview (Mirroring interface-mock-1.png) -->
            <div class="relative mt-12 lg:mt-0">
                <div class="absolute -inset-20 bg-ngn-blue/5 blur-[120px] rounded-full -z-10 animate-pulse-fast"></div>
                
                <div class="vst-panel p-1 rounded-sm shadow-2xl">
                    <div class="bg-[#0A0A0A] p-6 border border-white/5 space-y-8">
                        <!-- Top Bar -->
                        <div class="flex justify-between items-center border-b border-white/5 pb-4">
                            <span class="text-white font-sans font-bold tracking-tighter text-sm uppercase">NGN CLARITY</span>
                            <div class="h-1 w-20 bg-ngn-blue/30 rounded-full overflow-hidden">
                                <div class="h-full bg-ngn-blue w-2/3 shadow-[0_0_10px_rgba(0,210,255,1)]"></div>
                            </div>
                        </div>

                        <!-- Analyzer Visualization -->
                        <div class="h-48 bg-[#050505] border border-white/5 relative overflow-hidden">
                            <div class="absolute inset-0 opacity-20">
                                <div class="w-full h-full" style="background-image: linear-gradient(to right, #333 1px, transparent 1px), linear-gradient(to bottom, #333 1px, transparent 1px); background-size: 20px 20px;"></div>
                            </div>
                            <!-- Dynamic Waveform Stub -->
                            <svg class="absolute bottom-0 left-0 w-full h-32 text-ngn-blue/40" preserveAspectRatio="none" viewBox="0 0 400 100">
                                <path d="M0 80 Q 50 10, 100 80 T 200 80 T 300 20 T 400 80 V 100 H 0 Z" fill="currentColor" />
                                <path d="M0 80 Q 50 10, 100 80 T 200 80 T 300 20 T 400 80" stroke="currentColor" fill="none" stroke-width="2" class="text-ngn-blue shadow-[0_0_10px_#00D2FF]" />
                            </svg>
                        </div>

                        <!-- Prescription Readout -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-white/[0.02] border border-white/5">
                                <div class="text-[8px] uppercase tracking-widest text-white/40 mb-2">Spectral_Delta</div>
                                <div class="text-xl font-mono text-ngn-blue">88.4%</div>
                            </div>
                            <div class="p-4 bg-white/[0.02] border border-white/5">
                                <div class="text-[8px] uppercase tracking-widest text-white/40 mb-2">Dynamic_XP</div>
                                <div class="text-xl font-mono text-ngn-purple">12.1ms</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Inference Targets (Using local icons-flat.png logic) -->
    <section class="border-t border-white/5 bg-black/40 py-24">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
                <div>
                    <h2 class="text-xs uppercase tracking-[0.5em] text-ngn-blue font-bold mb-4 italic">// NEURAL_TRAINING_MODELS</h2>
                    <p class="text-3xl font-sans font-bold tracking-tight">Select your inference target.</p>
                </div>
                <div class="text-[10px] uppercase tracking-[0.3em] text-white/20 font-mono">
                    Updated: 2026-02-21 // Total_Stems: 52,104
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <?php 
                $targets = [
                    ['id' => 'kick', 'label' => 'KICK_DRUM'],
                    ['id' => 'snare', 'label' => 'SNARE_TOP'],
                    ['id' => 'bass', 'label' => 'BASS_DI'],
                    ['id' => 'guitar', 'label' => 'HI_GAIN_GTR'],
                    ['id' => 'vocals', 'label' => 'LEAD_VOCAL']
                ];
                foreach($targets as $t): ?>
                <div class="sp-card group hover:border-ngn-blue/50 transition-all duration-500 cursor-pointer">
                    <div class="h-20 w-20 mx-auto mb-6 opacity-60 group-hover:opacity-100 transition-opacity">
                        <?php echo Assets::getIcon($t['id'], 'icon-neon-purple'); ?>
                    </div>
                    <div class="text-center">
                        <div class="text-[9px] font-mono tracking-[0.3em] text-white/40 group-hover:text-ngn-blue transition-colors"><?php echo $t['label']; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Background Decoration -->
    <div class="absolute top-0 right-0 w-[800px] h-[800px] -translate-y-1/2 translate-x-1/4 opacity-10 pointer-events-none -z-10">
        <?php echo Assets::getEmblem('logo-solid-blue w-full h-full'); ?>
    </div>
</main>
