<main class="flex-grow max-w-7xl mx-auto px-6 py-20 w-full">
    <div class="mb-16">
        <h1 class="text-4xl md:text-6xl font-sans font-bold tracking-tighter mb-4 uppercase italic">INTEGRATION <span class="text-ngn-orange">PROTOCOLS.</span></h1>
        <p class="text-white/60 text-lg font-light">The technical bible for the NGN Clarity ecosystem.</p>
    </div>

    <div class="grid md:grid-cols-4 gap-12">
        <!-- Sidebar Navigation -->
        <aside class="md:col-span-1">
            <div class="sticky top-32 space-y-6 text-[10px] uppercase tracking-[0.2em] text-white/40">
                <div class="text-white/20 mb-4 tracking-[0.4em]">Sections</div>
                <a href="#quick-start" class="block hover:text-ngn-orange transition-colors">01 // Quick Start</a>
                <a href="#ai-engine" class="block hover:text-ngn-orange transition-colors">02 // AI Inference</a>
                <a href="#licensing" class="block hover:text-ngn-orange transition-colors">03 // Licensing & Auth</a>
                <a href="#fleet" class="block hover:text-ngn-orange transition-colors">04 // Fleet Sync</a>
                <a href="#telemetry" class="block hover:text-ngn-orange transition-colors">05 // Telemetry Ingest</a>
            </div>
        </aside>

        <!-- Documentation Content -->
        <article class="md:col-span-3 space-y-24 pb-40">
            <!-- Quick Start -->
            <section id="quick-start" class="scroll-mt-32">
                <h2 class="text-3xl font-sans font-bold tracking-tight mb-6 flex items-center gap-4">
                    <span class="text-ngn-orange/20 text-5xl">01</span>
                    Quick Start
                </h2>
                <div class="text-white/60 leading-relaxed font-light space-y-4">
                    <p>To initialize a new Clarity NGN node, you must first synchronize with the Graylight Foundry master repository.</p>
                    <div class="bg-black/40 p-6 border border-white/5 rounded font-mono text-xs text-ngn-orange">
                        nexus create-site clarity.nextgennoise.com<br>
                        nexus fleet-deploy
                    </div>
                </div>
            </section>

            <!-- AI Inference -->
            <section id="ai-engine" class="scroll-mt-32">
                <h2 class="text-3xl font-sans font-bold tracking-tight mb-6 flex items-center gap-4">
                    <span class="text-ngn-orange/20 text-5xl">02</span>
                    AI Inference Engine
                </h2>
                <p class="text-white/60 leading-relaxed font-light mb-6">
                    The NGN core utilizes local ONNX Runtime inference to analyze audio signals in real-time. This eliminates cloud latency and ensures data sovereignty.
                </p>
                <div class="sp-card border-ngn-orange/20">
                    <h4 class="text-xs uppercase tracking-widest text-ngn-orange mb-4">Model Specs</h4>
                    <ul class="text-xs space-y-2 text-white/40 uppercase tracking-widest">
                        <li>- Format: ONNX v1.18</li>
                        <li>- Precision: Float32</li>
                        <li>- Target: CPU / CoreML / DirectML</li>
                    </ul>
                </div>
            </section>

            <!-- Licensing -->
            <section id="licensing" class="scroll-mt-32">
                <h2 class="text-3xl font-sans font-bold tracking-tight mb-6 flex items-center gap-4">
                    <span class="text-ngn-orange/20 text-5xl">03</span>
                    Licensing & Auth
                </h2>
                <p class="text-white/60 leading-relaxed font-light mb-6">
                    Authentication is strictly handled via Beacon SSO. Every request must be signed with an HMAC-SHA256 signature using the Founder's Secret Key.
                </p>
                <div class="bg-black/40 p-6 border border-white/5 rounded font-mono text-xs text-white/30">
                    X-GL-SIGNATURE: hash_hmac('sha256', payload, secret)
                </div>
            </section>

            <!-- Fleet Sync -->
            <section id="fleet" class="scroll-mt-32">
                <h2 class="text-3xl font-sans font-bold tracking-tight mb-6 flex items-center gap-4">
                    <span class="text-ngn-orange/20 text-5xl">04</span>
                    Fleet Synchronicity
                </h2>
                <p class="text-white/60 leading-relaxed font-light">
                    The Sovereign Fleet consists of 21 specialized nodes. All nodes are atomically synchronized via the Nexus orchestrator. No local overrides are permitted in production environments.
                </p>
            </section>

            <!-- Telemetry -->
            <section id="telemetry" class="scroll-mt-32">
                <h2 class="text-3xl font-sans font-bold tracking-tight mb-6 flex items-center gap-4">
                    <span class="text-ngn-orange/20 text-5xl">05</span>
                    Telemetry Ingest
                </h2>
                <p class="text-white/60 leading-relaxed font-light mb-8">
                    Anonymized usage data is ingested by the Pulse node every 15 minutes to improve the A-OS brain.
                </p>
                <div class="bg-black/40 p-6 border border-white/5 rounded font-mono text-xs text-ngn-orange">
                    POST https://pulse.graylightcreative.com/ingest
                </div>
            </section>
        </article>
    </div>
</main>
