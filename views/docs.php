<main class="flex-grow max-w-5xl mx-auto px-6 py-20 w-full">
    <div class="mb-16">
        <h1 class="text-4xl md:text-6xl font-sans font-bold tracking-tighter mb-4">INTEGRATION <span class="text-ngn-orange">PROTOCOLS.</span></h1>
        <p class="text-white/60 text-lg font-light">The technical bible for the NGN Clarity ecosystem.</p>
    </div>

    <div class="grid md:grid-cols-4 gap-12">
        <!-- Sidebar Navigation -->
        <aside class="md:col-span-1 space-y-4 text-xs uppercase tracking-widest text-white/40">
            <a href="#" class="block text-ngn-orange font-bold">Quick Start</a>
            <a href="#" class="block hover:text-white transition-colors">AI Inference Engine</a>
            <a href="#" class="block hover:text-white transition-colors">Licensing & Auth</a>
            <a href="#" class="block hover:text-white transition-colors">Fleet Synchronicity</a>
            <a href="#" class="block hover:text-white transition-colors">Telemetry Ingest</a>
        </aside>

        <!-- Documentation Content -->
        <article class="md:col-span-3 prose prose-invert prose-orange max-w-none">
            <section class="mb-16">
                <h2 class="text-3xl font-sans font-bold tracking-tight mb-6">Introduction to <span class="text-ngn-orange">NGN Clarity</span></h2>
                <p class="text-white/60 leading-relaxed text-lg font-light mb-8">
                    NGN Clarity is a sovereign VST3 plugin designed to analyze mixing tracks against AI-learned targets and teach users how to fix issues using DAW stock plugins.
                </p>
                <div class="sp-card bg-ngn-orange/5 border-ngn-orange/20">
                    <h4 class="text-ngn-orange font-bold mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        Sovereign Integrity
                    </h4>
                    <p class="text-sm text-white/60 leading-relaxed">
                        All NGN Clarity nodes must adhere to the 90/10 Ledger mandate and use Beacon SSO for identity management. No local user tables are permitted.
                    </p>
                </div>
            </section>

            <section class="mb-16">
                <h2 class="text-2xl font-sans font-bold tracking-tight mb-6">AI Inference Pipeline</h2>
                <div class="bg-white/5 p-6 border border-white/5 rounded">
                    <pre class="text-xs text-ngn-orange"><code>
// Example Pulse Telemetry Ingest
{
  "hardware_id_hash": "e3b0c442...",
  "cpu_usage_avg": 45.2,
  "daw_name": "Studio One",
  "active_instrument_target": "drums",
  "timestamp": "2026-02-21T22:30:00Z"
}
                    </code></pre>
                </div>
            </section>
        </article>
    </div>
</main>
