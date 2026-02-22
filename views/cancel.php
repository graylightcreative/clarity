<main class="flex-grow max-w-7xl mx-auto px-6 py-20 w-full relative overflow-hidden flex items-center justify-center">
    <!-- Cancel HUD Background -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-white/5 blur-[150px] rounded-full -z-10"></div>
    
    <div class="sp-card border-white/20 max-w-2xl w-full p-12 text-center relative grayscale opacity-60">
        <!-- Cancel Reticle -->
        <div class="mb-10 flex justify-center">
            <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-white/40">
                <circle cx="40" cy="40" r="38" stroke="currentColor" stroke-width="0.5" stroke-dasharray="8 8"/>
                <path d="M30 30L50 50M50 30L30 50" stroke="currentColor" stroke-width="4" stroke-linecap="square"/>
                <circle cx="40" cy="40" r="25" stroke="currentColor" stroke-width="0.2" opacity="0.3"/>
            </svg>
        </div>

        <div class="space-y-6">
            <div class="inline-block px-4 py-1 bg-white/5 border border-white/10 text-white/40 text-[10px] uppercase tracking-[0.4em] font-bold italic hud-border">
                Mission_Result // Acquisition_Canceled
            </div>
            
            <h1 class="text-5xl md:text-7xl font-sans font-bold tracking-tighter italic uppercase">
                MISSION <br> <span class="text-white">ABORTED.</span>
            </h1>
            
            <p class="text-white/20 text-lg font-light leading-relaxed max-w-md mx-auto font-sans tracking-tight italic uppercase tracking-widest">
                Acquisition core was not pressurized. Checkout process terminated.
            </p>
        </div>

        <div class="mt-12">
            <a href="/purchase" class="btn-ngn w-full py-6 text-xl tracking-tighter italic uppercase bg-white/10 text-white/40 hover:bg-white/20">RETRY_ACQUISITION</a>
        </div>

        <div class="mt-8 text-[8px] uppercase tracking-[0.5em] text-white/5 font-mono italic">
            // Signal: payment_intent.canceled //
        </div>
    </div>
</main>
