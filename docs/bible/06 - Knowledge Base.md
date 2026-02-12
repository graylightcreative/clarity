06 The Knowledge Base

Project: The Mixing Mentor
Module: Translation Layer
Status: Defined

1. The DAW Map (Studio One Pro+)

This section acts as the lookup table. Since VST3 parameters are usually normalized floats (0.0 to 1.0), we must map specific settings to these float values for Studio One's stock plugins.

1.1 Target: Stock Compressor

Plugin ID: Presonus Compressor

Default Mode: Standard / FET

Parameter

Display Value

Norm. Float (Approx)

Notes

Ratio

4:1

0.40

Standard for Rock Vocals/Snare.

Ratio

8:1

0.75

Heavy compression (Parallel/Metal).

Attack

Fast (1ms)

0.01

For catching transients.

Attack

Slow (30ms)

0.15

To let "thwack" through.

Release

Auto

-1.0

Special switch value.

Threshold

(Variable)

Variable

This is the primary user control.

1.2 Target: Pro EQ 3

Plugin ID: Presonus Pro EQ

Band

Parameter

Function

Target Logic

LC (Low Cut)

Freq

HPF

"Clean up the mud" (Set to 60Hz-100Hz).

LF (Low Freq)

Gain

Shelf/Bell

"Add body" or "Remove boominess".

LMF (Low-Mid)

Gain

Bell

The Mud Zone (200-500Hz). Most common cut.

HMF (High-Mid)

Gain

Bell

The Attack Zone (2-4kHz). Add for click/definition.

HF (High Freq)

Gain

Shelf

"Air" (10kHz+).

2. Parameter Translation Logic

The AI outputs advice in Real World Units (milliseconds, decibels). The plugin must convert this to Normalized Floats for the logic engine to understand where the user's knob is.

The Math: RealToNorm()

We cannot assume linear mapping. Many audio knobs are logarithmic.

// Example: Converting "30ms Attack" to Studio One Float
float convertAttackToFloat(float ms) {
    // Studio One Compressor Attack typically log scale
    // 0.0 = 0.1ms, 0.5 = 20ms, 1.0 = 400ms (Hypothetical curve)
    
    // We use a Lookup Table (LUT) or approximation formula
    if (ms <= 1.0f) return 0.05f;
    if (ms <= 10.0f) return 0.25f;
    if (ms <= 30.0f) return 0.40f; 
    return 1.0f; // > 100ms
}


The Reverse: NormToReal()

When the user turns the knob, we read 0.40. We must display "30ms" to them if the DAW doesn't report text properly.

3. Genre Rules (The "Target Profiles")

This is the data we train against. When the user selects "Metalcore", these specific targets are loaded into the comparator.

3.1 Kick Drum (Metalcore)

Profile: "The Typewriter" (Clicky, tight, sub-heavy).

Dynamics:

Attack: < 5ms (Immediate).

Release: 50ms (Short/Gated).

Gain Reduction: -6dB to -10dB.

EQ Curve:

Sub (60Hz): +3dB boost.

Mud (250Hz): -6dB deep cut.

Click (4kHz): +5dB boost.

3.2 Snare Drum (Rock - Hard)

Profile: "The Cannon" (Deep, explosive, long tail).

Dynamics:

Attack: 15-30ms (Let the transient crack).

Release: 100-200ms (Musical pumping).

EQ Curve:

Body (200Hz): +2dB boost.

Boxiness (500Hz): -3dB cut.

Crack (3kHz): +2dB.

3.3 Bass Guitar (Modern)

Profile: "The Glue" (Consistent, compressed, sub-focused).

Relationship:

Sidechain: Must duck 3dB when Kick hits.

EQ Curve:

Sub (40-80Hz): Flat (Let Kick win 60Hz, Bass wins 80Hz).

Mids (800Hz): +3dB (For "growl" on small speakers).

4. The Advice Templates

When the AI detects a discrepancy, it fills in these "Mad Libs" style templates.

Template A (EQ Cut):
"Your [Instrument] is muddying the mix. Cut [Freq]Hz by [dB]dB on the [BandName]."

Template B (Compression - Punch):
"The transient is getting lost. Slow down the Attack to [ms]ms to let the punch through."

Template C (Masking - Global):
"This track is fighting with [OtherTrackName]. Try sidechaining or cutting [Freq]Hz here."