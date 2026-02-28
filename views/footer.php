<!-- Footer -->
    <footer class="bg-black border-t border-white/10 pt-32 pb-16 relative overflow-hidden">
        <!-- Deep Background Text for Aesthetic -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[20vw] font-sans font-bold text-white/[0.02] whitespace-nowrap pointer-events-none select-none uppercase tracking-tighter">
            SOVEREIGN_MIXING
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <!-- Main Logo Champion -->
            <div class="mb-20 flex flex-col items-center">
                <div class="w-32 h-32 md:w-40 md:h-40 mb-10">
                    <?php echo \Clarity\Core\Assets::getEmblem('w-full h-full logo-solid-blue drop-shadow-[0_0_30px_rgba(0,210,255,0.4)]'); ?>
                </div>
                <h2 class="text-6xl md:text-8xl font-sans font-bold tracking-tighter uppercase mb-4">
                    CLARITY <span class="text-ngn-blue">NGN</span>
                </h2>
                <p class="text-[12px] font-mono tracking-[0.6em] text-white/30 uppercase italic">
                    The Professional Mixing Assistant Protocol
                </p>
            </div>

            <!-- Fleet / Powered By Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-center border-y border-white/5 py-16 mb-20">
                <!-- NextGen Noise Champion -->
                <div class="flex flex-col items-center md:items-start gap-6">
                    <span class="text-[10px] font-mono tracking-[0.4em] text-white/20 uppercase">NextGen Noise Family</span>
                    <img src="https://nextgennoise.com/lib/images/site/2026/NGN-Logo-Full-Light.png" alt="NextGen Noise" class="h-10 opacity-60 hover:opacity-100 transition-opacity">
                </div>

                <!-- Center Fleet Status -->
                <div class="flex flex-col items-center gap-6">
                    <span class="text-[10px] font-mono tracking-[0.4em] text-white/20 uppercase">Fleet Connectivity</span>
                    <div class="flex gap-6 items-center">
                        <div class="w-6 h-6 opacity-30 hover:opacity-100 transition-all cursor-help" title="BEACON SSO"><?php echo \Clarity\Core\Assets::getIcon('HELP', 'text-white w-full h-full'); ?></div>
                        <div class="w-6 h-6 opacity-30 hover:opacity-100 transition-all cursor-help" title="CHANCELLOR FINANCE"><?php echo \Clarity\Core\Assets::getIcon('COMP', 'text-white w-full h-full'); ?></div>
                        <div class="w-6 h-6 opacity-30 hover:opacity-100 transition-all cursor-help" title="FUSE SIGNAL"><?php echo \Clarity\Core\Assets::getIcon('ANALYZE', 'text-white w-full h-full'); ?></div>
                        <div class="w-6 h-6 opacity-30 hover:opacity-100 transition-all cursor-help" title="NEXUS ORCHESTRATOR"><?php echo \Clarity\Core\Assets::getIcon('SETTINGS', 'text-white w-full h-full'); ?></div>
                    </div>
                </div>

                <!-- Graylight Creative Champion -->
                <div class="flex flex-col items-center md:items-end gap-6">
                    <span class="text-[10px] font-mono tracking-[0.4em] text-white/20 uppercase">Powered By Fleet</span>
                    <div class="text-right">
                        <div class="text-2xl font-sans font-bold tracking-tighter uppercase text-white/60">GRAYLIGHT <span class="text-ngn-purple">CREATIVE</span></div>
                        <div class="text-[8px] font-mono tracking-[0.5em] text-white/20 uppercase mt-1 italic">Sovereign Infrastructure</div>
                    </div>
                </div>
            </div>

            <!-- Legal / Navigation -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-12">
                <div class="flex flex-wrap gap-10 justify-center md:justify-start text-[11px] font-mono tracking-widest text-white/40 uppercase">
                    <a href="/" class="hover:text-ngn-blue transition-colors">Home_Base</a>
                    <a href="/docs" class="hover:text-ngn-blue transition-colors">Manual</a>
                    <a href="/purchase" class="hover:text-ngn-blue transition-colors">Acquire</a>
                    <span class="w-1 h-1 rounded-full bg-white/10 hidden md:block"></span>
                    <a href="#" class="hover:text-ngn-purple transition-colors">Privacy_Vlt</a>
                    <a href="#" class="hover:text-ngn-purple transition-colors">Terms_Svrn</a>
                </div>

                <div class="flex flex-col items-center md:items-end gap-4">
                    <div class="flex gap-2">
                        <div class="h-1 w-12 bg-ngn-blue/20 rounded-full overflow-hidden">
                            <div class="h-full bg-ngn-blue w-full animate-pulse"></div>
                        </div>
                        <div class="h-1 w-12 bg-ngn-purple/20 rounded-full overflow-hidden">
                            <div class="h-full bg-ngn-purple w-2/3"></div>
                        </div>
                    </div>
                    <div class="text-[9px] font-mono text-white/10 uppercase tracking-[0.5em]">
                        &copy; 2026 Graylight Creative // RIG_STND_V1.0.4 // ALL_RGHTS_RESERVED
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>