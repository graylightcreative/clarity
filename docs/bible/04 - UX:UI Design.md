04 UX/UI Design

Project: The Mixing Mentor
Module: User Experience
Status: Defined

1. The Core Philosophy: "The Feedback Loop"

This is not a passive analyzer. It is an active game loop. The UX is designed to guide the user through a specific "Tweak -> Listen -> Validate" cycle, similar to how Guitar Hero or Yousician gives instant feedback on performance.

The Interaction Cycle

Instruction: The UI displays a clear, actionable command based on the AI analysis.

Example: "Your attack is too fast. Increase Attack time to ~30ms to let the transient punch through."

Action: The user turns the knob on their Stock DAW Plugin (not our plugin).

Analysis: Our plugin listens to the audio change in real-time.

Validation:

Success: Green checkmark appears. "Perfect! Transient punch restored."

Fail: "Too far! You lost the body. Dial it back."

2. Visual Systems

We use two primary visual metaphors to guide the user without forcing them to read complex graphs.

2.1 The "Ghost Needle" (Dynamics)

Compression is the hardest concept for beginners. We solve this by visualizing the Target Gain Reduction.

The Component: A standard Gain Reduction (GR) meter.

The User's Needle (Grey): Shows the actual compression happening right now.

The Ghost Needle (Gold): A second, translucent needle that bounces where the AI wants the compression to be.

The Game: The user adjusts their compressor Threshold/Ratio until the Grey needle overlaps with the Gold Ghost needle.

2.2 The "Target Curve" (EQ)

Instead of a complex spectrum analyzer, we use a "Difference View."

The Center Line: Represents "Perfect Balance" (The Target).

The User's Curve:

Above Line (Red): Too much energy here (Mud/Harshness).

Below Line (Blue): Lacking energy here (Thin/Dull).

The Game: The user EQ's their track until the curve flattens out onto the Center Line.

2.3 The Text Feed (Priority Queue)

The AI might find 10 problems. We cannot show them all at once. The UI uses a "Priority Sort" to show only the Top 3 most critical issues.

Priority 1: "Phase Cancellation detected with Kick." (Critical)

Priority 2: "60Hz Low End is 6dB too loud." (Major)

Priority 3: "Slight harshness at 4kHz." (Minor - Hidden until P1/P2 are fixed).

3. The Session Setup ("The Band Manager")

When the user first loads the plugin, they are greeted by the Session Setup screen. This establishes the context for the "Accumulator" logic.

3.1 The Checklist View

A grid of instrument icons.

Prompt: "Who is in the band today?"

Selection: User clicks [Kick] [Snare] [Bass] [Guitars] [Vocals].

Result: The plugin reserves slots in the Global Session Manager for these instruments.

3.2 The Genre Selector

Dropdown: "Target Vibe"

Options:

Modern Metalcore: (Clicky kick, scooped mids, ultra-bright).

Dad Rock: (Thumpy kick, warm mids, dynamic).

Pop Punk: (Tight, fast, polished).

4. DAW Integration ("The Hand-Holding")

Since we are targeting Studio One Pro+ for Iteration 1, the UI will explicitly reference Studio One stock plugins.

Context-Aware Instructions

Instead of generic advice ("Use a FET Compressor"), the UI will display:

"Load 'Compressor' (Stock)"

Set Mode to FET.

Set Ratio to 4:1.

Adjust Threshold until the Ghost Needle matches.

(In future iterations, this text updates dynamically if the plugin detects the user is in Logic Pro or Reaper).

5. Wireframe Layout (Main Window)

+-------------------------------------------------------+
|  [ HEADER: Instrument Name (Kick) | Genre (Metal) ]   |
|                                                       |
|  [ LEFT: The Feed ]          [ RIGHT: Visuals ]       |
|  1. "Cut 200Hz by 3dB"       |                        |
|     [Status: Pending...]     |    (EQ Target Curve)   |
|                              |                        |
|  2. "Compress 4dB"           |    (Ghost Needle)      |
|     [Status: Good!]          |                        |
|                              |                        |
+-------------------------------------------------------+
|  [ FOOTER: Context ]                                  |
|  "Relation: Bass is masking this track at 60Hz"       |
+-------------------------------------------------------+
