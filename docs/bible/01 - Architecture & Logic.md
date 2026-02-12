01 Architecture & Logic

Project: The Mixing Mentor
Module: Core Logic
Status: Defined

1. The Distributed Satellite System

Most VST plugins live on an island—they only know about the track they are on. To function as a "Mentor," our plugin must have Global Awareness. It needs to know that the "Kick" exists while you are working on the "Bass."

The Pattern: "Hive Mind" Singleton

We utilize a Global Singleton (SessionManager) in C++ that sits outside the individual track instances.

The Satellites (Track Instances): Every time you load the plugin on a track, it is a "Satellite." It does two things:

Local Analysis: Listens to the audio on its own track.

Data Push: Sends a lightweight "Fingerprint" of that audio to the Global Singleton.

The Hub (Session Manager): A static memory block that holds the TrackProfile for every instance in the session.

Communication Flow

User opens plugin on Bass.

Bass Instance asks Session Manager: "Who else is here?"

Session Manager replies: "I have a Kick Drum profile from Track 1."

Bass Instance downloads the Kick's Fingerprint.

Comparison: The plugin analyzes the live Bass audio vs. the stored Kick data to detect frequency masking.

2. State Management (The "Wake-on-Edit" Cycle)

To allow this to run on average laptops, we cannot have 30 instances running complex AI analysis simultaneously. We use a strict state machine to manage CPU resources.

State A: Active (The Teacher)

Trigger: The User OPENS the plugin window (GUI).

Function: Real-time FFT (Fast Fourier Transform), AI Inference, UI rendering.

CPU Cost: High.

Role: This is the only time the plugin is "Listening" and "Teaching."

State B: Passive (The Memory)

Trigger: The User CLOSES the plugin window.

Function: The audio processing bypasses the heavy analysis code. It essentially does nothing but hold the last known Fingerprint in memory.

CPU Cost: Near Zero.

Role: Acts as a reference point for other tracks to compare against.

State C: Re-Scan (Wake-on-Edit)

Trigger: The User re-opens the plugin window on a track that was previously analyzed.

Function: The plugin assumes the user might have changed EQ/Compression since the last visit. It initiates a 3-5 second "Re-Scan" to update the Fingerprint.

UI Feedback: "Listening... updating profile..."

3. The Accumulator (Context Awareness)

The AI does not just fix tracks in a vacuum; it fixes them in Context. We enforce a mixing hierarchy called "The Accumulator."

The Logic Stack

The plugin categorizes tracks into Layers. Each layer checks itself against the layers below it.

Order

Layer

Role

Logic / Checks

1

Foundation

Kick, Snare

Self-Contained. Checks against Genre Targets (e.g., "Is this Kick punchy enough for Metal?").

2

Groove

Bass

Relational. Checks against Foundation. (e.g., "Is the Bass masking the Kick's fundamental freq?").

3

Body

Guitars, Keys

Relational. Checks against Groove + Foundation. (e.g., "Are guitars muddying up the Bass?").

4

Soul

Vocals

Relational. Checks against Everything. (e.g., "Can the vocals cut through the wall of guitars?").

The "Missing Link" Warning

If the user breaks the order—for example, trying to mix Vocals before the Drums are set up—the Mentor provides a "Soft Warning."

User: Loads plugin on Vocals.

AI Check: "Do we have a Drum Bus or Kick committed?"

Result: No.

Advice: "Warning: No Rhythm Section detected. I can help you EQ the voice, but I cannot guarantee it will sit in the mix later."

4. The Decision Tree (The Brain)

When the Active Instance analyzes audio, it runs through this logic loop:

Identify: What instrument is this? (User Input or Auto-Detect).

Target: Load the GenreTarget (e.g., Metalcore Snare).

Analyze: Measure current spectral balance and dynamics.

Compare (Local): How far is Current from Target? -> Generate EQ/Comp advice.

Compare (Global): Fetch SessionManager data. Is Current clashing with Foundation? -> Generate Masking advice.

Display: Priority Sort the advice (Fix the biggest problem first) and show on UI.