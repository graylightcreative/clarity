The Mixing Mentor: Project Bible

Version: 0.1 (Concept Phase)
Target: Iteration 1 (Rock/Metal for Studio One Pro+)

📜 Mission Statement

To build a "Yousician for Mixing" VST3 plugin that acts as a Virtual Mentor. Instead of processing audio automatically, it listens to the user's mix, analyzes it against "Gold Standard" AI targets, and teaches the user how to fix their tracks using their DAW's stock plugins.

📚 Table of Contents

01_Architecture_and_Logic.md

The Distributed Satellite System: How multiple instances communicate.

State Management: Active vs. Passive (Sleep) modes.

The Accumulator: How the plugin understands context (Drum bus first, then Bass, etc.).

02_Data_Structures.md

The Track Model: Struct definitions for ID, Name, Instrument, and Fingerprints.

The Global Engine: Singleton class design for session awareness.

Fingerprinting: How we store spectral data and dynamic range snapshots.

03_AI_Strategy_and_Training.md

Contrast Training: Using Suno AI to generate "Good" vs. "Bad" datasets.

TinyML Implementation: TensorFlow Lite / ONNX Runtime architecture.

Feature Extraction: FFT, MFCC, and Dynamic profiling.

04_UX_UI_Design.md

The Feedback Loop: How the plugin instructs the user (Text Feed + Audio Feedback).

Visual Aids: The "Ghost Needle" concept and target overlays.

Session Setup: The band lineup checklist and "Add-as-you-go" workflow.

05_Tech_Stack_and_Environment.md

Core: C++20, JUCE Framework.

AI: Python (Training), C++ (Inference).

Target DAW: Studio One Pro+.

Dev Tools: CMake, Git, CLI Agents.

06_The_Knowledge_Base.md

The DAW Map: Database of Studio One Pro+ stock plugins (Compressor, Pro EQ, Gate).

Parameter Translation: Mapping generic AI values (e.g., "Fast Attack") to specific VST parameter floats (0.0 - 1.0).

Genre Rules: The specific frequency targets for "Metalcore Kick" vs "Rock Snare".

08_Licensing_Strategy.md

VST Plugin-side licensing (machine fingerprinting, trial management, online/offline activation).

License encryption and anti-piracy measures.

Integration with web backend for validation.

09_Web_Infrastructure.md

Backend API (authentication, licensing, payments, analytics).

Frontend customer portal (homepage, account management, downloads).

Database architecture and payment processing.

Email system and transactional emails (smtp.com + Mailchimp).

10_aapanel_Deployment_&_Subscription_Setup.md

aapanel server setup and configuration.

PostgreSQL, Redis, and Nginx on aapanel.

Cron jobs for license expiration, trial expiration, Mailchimp sync, analytics aggregation.

Email configuration via smtp.com.

Monitoring, backups, and security hardening.

🔑 Key Definitions

Term

Definition

The Mentor

The VST plugin acting as the teacher.

Fingerprint

A low-CPU snapshot of a track's frequency and dynamic characteristics.

Accumulator

The logic that builds the mix layer-by-layer (Drums -> Bass -> etc.).

Wake-on-Edit

The trigger that forces a plugin to re-scan a track when the window is opened.

Ghost Needle

Visual UI element showing where the user's compression/levels should be.

The Map

The lookup table connecting our AI's advice to specific Studio One knobs.

🛠 Project Phases

Phase 0: Project Setup - Initialize git, folder structure, build system, documentation.

Phase 1: The Brain (Python) - generating the logic and training the model using Suno data.

Phase 2: The Shell (C++/JUCE) - Basic plugin architecture and inter-plugin communication.

Phase 2A: Licensing Integration - Machine fingerprinting, trial management, activation.

Phase 3: The Integration - Embedding the Brain into the Shell.

Phase 4: The UX - Building the "Ghost Needle" and instructional text feed.

Phase 5: Studio One Beta - Testing strictly within the Studio One environment.

Phase 6: Iteration 2+ Planning - Future genres and multi-DAW support.

Phase 7: Web Backend - API server, authentication, licensing, payments, analytics.

Phase 8: Web Frontend - Customer portal, purchases, account management.

Phase 9: Database Design - PostgreSQL schema, migrations, Redis caching.

Phase 10: Installer & Distribution - Windows/macOS installers, code signing, auto-updates.

Phase 11: Email, Admin, Monitoring & Security - Email service, admin dashboard, error tracking, GDPR compliance.