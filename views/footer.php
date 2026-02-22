    <!-- Footer -->
    <footer class="border-t border-white/5 py-24 bg-black mt-auto relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-ngn-blue/5 to-transparent opacity-20 pointer-events-none"></div>
        
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-start mb-20">
                <div class="space-y-10">
                    <div class="flex items-center gap-6 group">
                        <div class="w-16 h-16">
                            <?php echo \Clarity\Core\Assets::getEmblem('w-full h-full text-ngn-blue drop-shadow-[0_0_15px_rgba(0,210,255,0.5)]'); ?>
                        </div>
                        <div>
                            <h3 class="text-4xl font-sans font-bold tracking-tighter">CLARITY <span class="text-ngn-blue">NGN</span></h3>
                            <p class="text-[10px] font-mono tracking-[0.4em] text-white/40 uppercase italic mt-1">Sovereign Mixing Intelligence</p>
                        </div>
                    </div>
                    
                    <p class="text-xl text-white/60 font-sans font-light leading-relaxed max-w-md">
                        The definitive mixing assistant for the modern producer. Built for speed, precision, and absolute sonic sovereignty.
                    </p>

                    <div class="pt-4">
                        <div class="text-[10px] font-mono tracking-[0.5em] text-white/30 uppercase mb-6 flex items-center gap-4">
                            <span class="h-[1px] w-8 bg-white/10"></span>
                            The Graylight Fleet
                            <span class="h-[1px] w-8 bg-white/10"></span>
                        </div>
                        <div class="flex gap-6 items-center opacity-40 hover:opacity-100 transition-opacity duration-700">
                            <div class="w-6 h-6" title="BEACON SSO"><?php echo \Clarity\Core\Assets::getIcon('HELP', 'text-white w-full h-full'); ?></div>
                            <div class="w-6 h-6" title="CHANCELLOR FINANCE"><?php echo \Clarity\Core\Assets::getIcon('COMP', 'text-white w-full h-full'); ?></div>
                            <div class="w-6 h-6" title="FUSE SIGNAL"><?php echo \Clarity\Core\Assets::getIcon('ANALYZE', 'text-white w-full h-full'); ?></div>
                            <div class="w-6 h-6" title="NEXUS DEPLOY"><?php echo \Clarity\Core\Assets::getIcon('SETTINGS', 'text-white w-full h-full'); ?></div>
                            <div class="w-12 h-[1px] bg-white/10 mx-2"></div>
                            <div class="text-[8px] font-mono tracking-widest text-white/40">FLEET_SYNC_ACTIVE</div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-12">
                    <div class="space-y-6">
                        <h4 class="text-[10px] font-mono tracking-[0.4em] text-ngn-blue uppercase font-bold">Navigation</h4>
                        <div class="flex flex-col gap-4 text-xs font-sans text-white/40">
                            <a href="/" class="hover:text-white transition-colors uppercase tracking-widest">Home_Base</a>
                            <a href="/docs" class="hover:text-white transition-colors uppercase tracking-widest">Protocols</a>
                            <a href="/purchase" class="hover:text-white transition-colors uppercase tracking-widest">Acquire</a>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <h4 class="text-[10px] font-mono tracking-[0.4em] text-ngn-purple uppercase font-bold">Sovereignty</h4>
                        <div class="flex flex-col gap-4 text-xs font-sans text-white/40">
                            <a href="#" class="hover:text-white transition-colors uppercase tracking-widest">Privacy_Vlt</a>
                            <a href="#" class="hover:text-white transition-colors uppercase tracking-widest">Terms_Svrn</a>
                            <a href="/login" class="hover:text-white transition-colors uppercase tracking-widest">Terminal</a>
                        </div>
                    </div>
                    <div class="space-y-6 hidden md:block">
                        <h4 class="text-[10px] font-mono tracking-[0.4em] text-ngn-orange uppercase font-bold">Network</h4>
                        <div class="flex flex-col gap-4 text-xs font-sans text-white/40">
                            <a href="https://nextgennoise.com" target="_blank" class="hover:text-white transition-colors uppercase tracking-widest">NGN_Main</a>
                            <a href="https://graylightcreative.com" target="_blank" class="hover:text-white transition-colors uppercase tracking-widest">Graylight</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/5 pt-12 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-6">
                    <img src="https://nextgennoise.com/lib/images/site/2026/NGN-Logo-Full-Light.png" alt="NextGen Noise" class="h-6 opacity-40">
                    <div class="h-4 w-[1px] bg-white/10"></div>
                    <p class="text-[9px] font-mono tracking-[0.3em] text-white/20 uppercase">
                        Part of the <span class="text-white/40">NextGen Noise Family</span>. <br class="md:hidden"> Powered by the <span class="text-white/40">Graylight Creative Fleet</span>.
                    </p>
                </div>

                <div class="flex items-center gap-8">
                    <div class="text-[8px] font-mono text-white/10 uppercase tracking-[0.5em]">
                        &copy; 2026 Graylight Creative // RIG_STND_V1
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1 h-1 rounded-full bg-ngn-blue animate-pulse"></div>
                        <div class="w-1 h-1 rounded-full bg-ngn-purple"></div>
                        <div class="w-1 h-1 rounded-full bg-ngn-orange"></div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
