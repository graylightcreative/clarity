03 AI Strategy & Training

Project: The Mixing Mentor
Module: The Brain (TinyML)
Status: Defined

1. The Core Philosophy: "Contrast Training"

Traditional AI tries to generate audio. We don't want to generate; we want to Critique.
To teach a machine what "Good" sounds like, we must also show it what "Bad" sounds like.

The Dataset Strategy

Since we cannot legally scrape copyrighted commercial tracks for a commercial product, we will use Suno AI to generate royalty-free training data.

A. The "Gold" Set (Target)

Prompt Strategy: "Audiophile quality, [Genre], perfect mix, wide stereo image, punchy kick, crisp snare, high fidelity."

Format: Stem Separation (v3/v4).

Labeling: class: ideal, genre: metalcore, instrument: kick.

B. The "Flawed" Set (Input)

Prompt Strategy: "Lo-fi, garage demo, [Genre], muddy mix, boxy kick, thin snare, distorted, room reverb, phase issues."

Format: Stem Separation.

Labeling: class: flawed, flaw_type: muddy_200hz, flaw_type: no_transient.

2. Feature Extraction (The "Fingerprint")

Before the AI sees the audio, we strip it down to numbers. We do not feed raw waveforms into the neural net (too heavy). We feed it the Fingerprint.

The Input Vector (64 Float Values)

Spectral Balance (32 floats): 1/3 Octave band energy averages.

Spectral Centroid (1 float): Center of mass of the spectrum (Brightness).

Spectral Flatness (1 float): Noise-like vs. Tone-like.

Crest Factor (1 float): Peak-to-RMS ratio (Punchiness).

Zero Crossing Rate (1 float): Noisiness/Frequency proxy.

MFCCs (13 floats): Mel-frequency cepstral coefficients (Timbre/Texture).

Delta MFCCs (13 floats): Rate of change of timbre (Movement).

3. Model Architecture (The "Brain")

We require a model that runs on a standard CPU with < 2ms latency. We will use a Random Forest Regressor or a Quantized MLP (Multi-Layer Perceptron).

The Task: Regression

We are not just classifying ("Good" vs "Bad"). We are predicting the Correction Vector.

Input: User_Kick_Fingerprint

Target: Ideal_Kick_Fingerprint

Output: Difference_Vector (e.g., "-3dB at 200Hz", "+2dB at 8kHz").

Technology Stack

Training (Python): scikit-learn or TensorFlow.

Export Format: .onnx (Open Neural Network Exchange).

Inference (C++): ONNX Runtime (Microsoft) integrated into JUCE.

Why ONNX?
It allows us to train in Python and run in C++ with zero friction. It is industry standard for VSTs.

4. The Training Pipeline (Python Script Logic)

This is the roadmap for the Python script we will write in Phase 1.

Ingest: Iterate through training_data/good/ and training_data/bad/.

Pre-process: Normalize loudness to -18 LUFS (to match typical mixing levels).

Feature Extraction: Use librosa to calculate the 64-float vector for every 100ms chunk of audio.

Labeling:

Good files = Target Vector (0 correction needed).

Bad files = Calculate the spectral difference between this "Bad" file and the average "Good" file. That difference is the Label.

Train: Fit the model to predict the Difference based on the Input Features.

Export: Save as brain_metalcore_v1.onnx.

5. Runtime Inference (C++)

When the user plays their track in Studio One:

Buffer Block: VST receives a buffer of audio samples.

Feature Extraction: C++ DSP extracts the same 64 values as Python librosa did.

Inference: Pass vector to ONNX Runtime.

Result: Model returns the predicted adjustments.

Smoothing: Apply a moving average filter to the results so the advice doesn't jitter wildly 10 times a second.

UI Update: Text feed says: "Cut 200Hz to reduce mud."