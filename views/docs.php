<main class="flex-grow max-w-7xl mx-auto px-6 py-20 w-full">
    <div class="mb-16">
        <h1 class="text-4xl md:text-6xl font-sans font-bold tracking-tighter mb-4 uppercase italic tracking-[-0.05em]">KNOWLEDGE <span class="text-ngn-orange">DATABASE.</span></h1>
        <p class="text-white/60 text-lg font-light max-w-2xl">The technical and operational manual for NGN Clarity and the Graylight Foundry.</p>
    </div>

    <div class="grid md:grid-cols-4 gap-12">
        <!-- Sidebar Navigation -->
        <aside class="md:col-span-1">
            <div class="sticky top-32 space-y-12">
                <!-- Group 1: Product -->
                <div class="space-y-4">
                    <div class="text-[10px] uppercase tracking-[0.4em] text-white/20 mb-6 border-b border-white/5 pb-2">Product Manual</div>
                    <div class="space-y-4 text-[10px] uppercase tracking-[0.2em] text-white/40">
                        <a href="#mentor-mission" class="block hover:text-ngn-orange transition-colors">00 // The Mission</a>
                        <a href="#how-it-works" class="block hover:text-ngn-orange transition-colors">01 // AI Intelligence</a>
                        <a href="#the-prescription" class="block hover:text-ngn-orange transition-colors">02 // The Prescription</a>
                        <a href="#sovereign-link" class="block hover:text-ngn-orange transition-colors font-bold text-white/60">03 // Sovereign Link</a>
                        <a href="#workflow" class="block hover:text-ngn-orange transition-colors">04 // Mixing Workflow</a>
                    </div>
                </div>

                <!-- Group 2: Technical -->
                <div class="space-y-4">
                    <div class="text-[10px] uppercase tracking-[0.4em] text-white/20 mb-6 border-b border-white/5 pb-2">Integration Protocols</div>
                    <div class="space-y-4 text-[10px] uppercase tracking-[0.2em] text-white/40">
                        <a href="#licensing" class="block hover:text-ngn-orange transition-colors">05 // Licensing & Auth</a>
                        <a href="#telemetry" class="block hover:text-ngn-orange transition-colors">06 // Telemetry Ingest</a>
                        <a href="#quick-start" class="block hover:text-ngn-orange transition-colors">07 // Admin Protocols</a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Documentation Content -->
        <article class="md:col-span-3 space-y-32 pb-60">
            
            <!-- PRODUCT MANUAL SECTIONS -->

            <!-- The Mission -->
            <section id="mentor-mission" class="scroll-mt-32">
                <h2 class="text-4xl font-sans font-bold tracking-tight mb-8">
                    <span class="text-ngn-orange/20 text-6xl block mb-2 font-mono">00</span>
                    The Mission: <span class="text-ngn-orange">Education via Analysis.</span>
                </h2>
                <div class="text-white/70 leading-relaxed font-light space-y-6 text-lg">
                    <p>NGN Clarity is not a "magic button" plugin. It is an AI-powered **Mixing Mentor** designed to bridge the gap between amateur results and professional clarity. </p>
                    <p>Unlike automated mixing tools that hide their logic, Clarity visualizes the delta between your audio and world-class reference targets, teaching you the "Why" behind every move.</p>
                </div>
            </section>

            <!-- AI Intelligence -->
            <section id="how-it-works" class="scroll-mt-32">
                <h2 class="text-4xl font-sans font-bold tracking-tight mb-8">
                    <span class="text-ngn-orange/20 text-6xl block mb-2 font-mono">01</span>
                    AI Intelligence: <span class="text-ngn-orange">The Neural Ear.</span>
                </h2>
                <div class="text-white/70 leading-relaxed font-light space-y-6">
                    <p>At the heart of Clarity is a specialized neural network trained on over 50,000 professional stems across every major genre. </p>
                    <p>The plugin performs a real-time, multi-dimensional analysis of your track's frequency response, dynamic range, and harmonic content. It then compares this data to high-integrity **Inference Targets**—idealized sonic profiles for specific instruments.</p>
                    <div class="sp-card bg-white/[0.02] border-white/5">
                        <h4 class="text-xs uppercase tracking-widest text-ngn-orange mb-4 font-mono">Core Tech: Local Inference</h4>
                        <p class="text-sm text-white/40">100% of the AI analysis is performed locally on your CPU/GPU via ONNX Runtime. Your audio never leaves your DAW. Zero latency. Total sovereignty.</p>
                    </div>
                </div>
            </section>

            <!-- The Prescription -->
            <section id="the-prescription" class="scroll-mt-32">
                <h2 class="text-4xl font-sans font-bold tracking-tight mb-8">
                    <span class="text-ngn-orange/20 text-6xl block mb-2 font-mono">02</span>
                    The Prescription: <span class="text-ngn-orange">Stock-Plugin Mastery.</span>
                </h2>
                <div class="text-white/70 leading-relaxed font-light space-y-6">
                    <p>Once the analysis is complete, the Mentor issues a **Prescription**. This is a prioritized list of sonic corrections tailored specifically to your track.</p>
                    <p>The Mentor teaches you how to execute these corrections using **DAW Stock Plugins**. By mastering EQ, Compression, and Saturation on the tools you already own, you build skills that transcend any specific piece of gear.</p>
                </div>
            </section>

            <!-- Sovereign Link -->
            <section id="sovereign-link" class="scroll-mt-32">
                <h2 class="text-4xl font-sans font-bold tracking-tight mb-8">
                    <span class="text-ngn-orange/20 text-6xl block mb-2 font-mono">03</span>
                    Sovereign Link: <span class="text-ngn-orange">The Desktop Gateway.</span>
                </h2>
                <div class="text-white/70 leading-relaxed font-light space-y-8">
                    <p>The **Sovereign Link** is the dedicated companion application for Windows and macOS. It handles all background synchronization, ensuring your VST engine is always pressurized without requiring manual intervention or terminal commands.</p>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="p-6 bg-white/[0.02] border border-white/5 rounded-lg">
                            <h4 class="text-ngn-orange text-xs uppercase tracking-[0.2em] mb-4">Identity & Activation</h4>
                            <p class="text-sm text-white/40">Log in once with your Beacon SSO credentials to authorize your workstation. The Link handles the HMAC-SHA256 handshake with the Vault automatically.</p>
                        </div>
                        <div class="p-6 bg-white/[0.02] border border-white/5 rounded-lg">
                            <h4 class="text-ngn-orange text-xs uppercase tracking-[0.2em] mb-4">Model Synchronization</h4>
                            <p class="text-sm text-white/40">The Link automatically pulls the latest ONNX AI models and inference targets from the Studio node, keeping your Mentor's "brain" updated in real-time.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Workflow -->
            <section id="workflow" class="scroll-mt-32">
                <h2 class="text-4xl font-sans font-bold tracking-tight mb-8">
                    <span class="text-ngn-orange/20 text-6xl block mb-2 font-mono">04</span>
                    Mixing Workflow: <span class="text-ngn-orange">The Sovereign Path.</span>
                </h2>
                <div class="space-y-12">
                    <div class="flex items-start gap-8">
                        <div class="w-12 h-12 rounded bg-ngn-orange text-ngn-charcoal flex items-center justify-center font-bold font-mono shrink-0">01</div>
                        <div>
                            <h4 class="text-xl font-bold mb-2">Initialize Sovereign Link</h4>
                            <p class="text-white/40 font-light">Launch the Link app, log in, and ensure your Node Status is <span class="text-ngn-orange">PRESSURIZED</span>.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-8">
                        <div class="w-12 h-12 rounded bg-ngn-orange text-ngn-charcoal flex items-center justify-center font-bold font-mono shrink-0">02</div>
                        <div>
                            <h4 class="text-xl font-bold mb-2">Analyze Track</h4>
                            <p class="text-white/40 font-light">Open the Clarity VST in your DAW, select a target, and capture 5 seconds of audio.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-8">
                        <div class="w-12 h-12 rounded bg-ngn-orange text-ngn-charcoal flex items-center justify-center font-bold font-mono shrink-0">03</div>
                        <div>
                            <h4 class="text-xl font-bold mb-2">Execute Prescription</h4>
                            <p class="text-white/40 font-light">Follow the Mentor's visual guide and apply the fixes using your stock plugins.</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="h-px bg-white/5 my-20"></div>

            <!-- INTEGRATION PROTOCOLS (The Technical Side) -->

            <section id="licensing" class="scroll-mt-32">
                <h2 class="text-3xl font-sans font-bold tracking-tight mb-6 flex items-center gap-4">
                    <span class="text-ngn-orange/20 text-5xl font-mono">05</span>
                    Licensing & Auth
                </h2>
                <p class="text-white/60 leading-relaxed font-light mb-6">
                    Authentication is strictly handled via Beacon SSO. Every request must be signed with an HMAC-SHA256 signature using the Founder's Secret Key.
                </p>
                <div class="bg-black/40 p-6 border border-white/5 rounded font-mono text-xs text-white/30">
                    X-GL-SIGNATURE: hash_hmac('sha256', payload, secret)
                </div>
            </section>

            <section id="telemetry" class="scroll-mt-32">
                <h2 class="text-3xl font-sans font-bold tracking-tight mb-6 flex items-center gap-4">
                    <span class="text-ngn-orange/20 text-5xl font-mono">06</span>
                    Telemetry Ingest
                </h2>
                <p class="text-white/60 leading-relaxed font-light mb-8">
                    Anonymized usage data is ingested by the Pulse node every 15 minutes to improve the A-OS brain.
                </p>
                <div class="bg-black/40 p-6 border border-white/5 rounded font-mono text-xs text-ngn-orange">
                    POST https://pulse.graylightcreative.com/ingest
                </div>
            </section>

            <section id="quick-start" class="scroll-mt-32">
                <h2 class="text-3xl font-sans font-bold tracking-tight mb-6 flex items-center gap-4">
                    <span class="text-ngn-orange/20 text-5xl font-mono">07</span>
                    Admin Protocols
                </h2>
                <div class="text-white/60 leading-relaxed font-light space-y-4">
                    <p>To initialize a new Clarity NGN node in the Sovereign Fleet:</p>
                    <div class="bg-black/40 p-6 border border-white/5 rounded font-mono text-xs text-ngn-orange">
                        nexus create-site clarity.nextgennoise.com<br>
                        nexus fleet-deploy
                    </div>
                </div>
            </section>
        </article>
    </div>
</main>
