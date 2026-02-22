<main class="flex-grow max-w-7xl mx-auto px-6 py-20 w-full relative overflow-hidden flex items-center justify-center">
    <!-- Success HUD Background -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-ngn-orange/5 blur-[150px] rounded-full -z-10 animate-pulse-fast"></div>
    
    <div class="sp-card border-ngn-orange max-w-2xl w-full p-12 text-center relative">
        <!-- Success Reticle -->
        <div class="mb-10 flex justify-center">
            <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-ngn-orange">
                <circle cx="40" cy="40" r="38" stroke="currentColor" stroke-width="0.5" stroke-dasharray="8 8"/>
                <path d="M25 40L35 50L55 30" stroke="currentColor" stroke-width="4" stroke-linecap="square"/>
                <circle cx="40" cy="40" r="25" stroke="currentColor" stroke-width="0.2" opacity="0.3"/>
            </svg>
        </div>

        <div class="space-y-6">
            <div class="inline-block px-4 py-1 bg-ngn-orange/10 border border-ngn-orange/30 text-ngn-orange text-[10px] uppercase tracking-[0.4em] font-bold italic hud-border">
                Mission_Result // Acquisition_Success
            </div>
            
            <h1 class="text-5xl md:text-7xl font-sans font-bold tracking-tighter italic uppercase">
                MISSION <br> <span class="text-ngn-orange">AUTHORIZED.</span>
            </h1>
            
            <p class="text-white/40 text-lg font-light leading-relaxed max-w-md mx-auto font-sans tracking-tight">
                Your Founder's Acquisition has been pressurized. The **Sovereign Link** is now synchronizing your monthly Spark allocation.
            </p>
        </div>

        <div class="mt-12 pt-12 border-t border-white/5 grid grid-cols-2 gap-8">
            <div class="text-left">
                <div class="text-[8px] uppercase tracking-[0.3em] text-white/20 mb-1 font-bold">Node_Status:</div>
                <div class="text-sm font-mono text-ngn-orange font-bold uppercase italic">PRESSURIZED</div>
            </div>
            <div class="text-right">
                <div class="text-[8px] uppercase tracking-[0.3em] text-white/20 mb-1 font-bold">Monthly_Sparks:</div>
                <div class="text-sm font-mono text-white font-bold uppercase italic">500 / MO</div>
            </div>
        </div>

        <div class="mt-12">
            <a href="/docs" class="btn-ngn w-full py-6 text-xl tracking-tighter italic uppercase">READ_MISSION_PROTOCOLS</a>
        </div>

        <div class="mt-8 text-[8px] uppercase tracking-[0.5em] text-white/10 font-mono italic">
            // Signal: payment_intent.succeeded received from FUSE //
        </div>
    </div>
</main>
