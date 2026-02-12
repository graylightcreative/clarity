05 Tech Stack & Environment

Project: The Mixing Mentor
Module: Infrastructure
Status: Defined

1. The Core Stack (Plugin Shell)

This is the foundation. We are building a VST3 plugin that must run seamlessly on Windows and macOS.

Language: C++20 (Required for modern concepts and cleaner syntax).

Framework: JUCE 8 (or latest stable).

Why? Industry standard, cross-platform, handles all the messy audio drivers and VST3 wrapping for us.

Build System: CMake 3.25+.

Why? JUCE now defaults to CMake. It allows us to generate project files for both Visual Studio (Windows) and Xcode (Mac) from a single script.

2. The AI Stack (The Brain)

This project has two distinct AI environments: Training (Offline) and Inference (Real-time).

2.1 Training Environment (Python)

This runs locally on your machine to "learn" from the Suno files.

Language: Python 3.10+.

Key Libraries:

librosa: For extracting audio features (MFCCs, Spectral Centroid) from .wav files.

scikit-learn: For training the Random Forest or MLP Regressor.

onnx: For exporting the trained model to a standard format.

numpy/pandas: For data manipulation.

2.2 Inference Environment (C++)

This runs inside the plugin when the user is mixing.

Runtime: ONNX Runtime (C++ API).

Why? Microsoft's high-performance engine. It reads the .onnx file we made in Python and runs it in microseconds.

Integration: We will link onnxruntime as a static library in CMake.

3. The Development Environment

To ensure our CLI Agent can generate valid code, we need to standardize the local setup.

IDE & Compilers

Windows: Visual Studio 2022 Community (MSVC).

macOS: Xcode 15+ (Clang).

Version Control

Git: Standard branching model.

main: Stable releases.

dev: Active development.

feature/ai-brain: Specific module work.

CLI Agent Workflow

Since you are using a CLI Agent to write the code, we will structure the repo to be "Agent-Friendly":

Modular Files: Small .cpp files (under 300 lines) so the Agent doesn't lose context.

Header-Heavy: We define logic in .h files first (which costs fewer tokens to read) before asking the Agent to implement the .cpp.

4. The Target Environment (Host)

We are optimizing specifically for Studio One Pro+.

Plugin Format: VST3 (64-bit only).

Note: We will disable AU/AAX formats for Iteration 1 to speed up compile times.

Host Specifics:

We utilize IEditController interfaces to detect we are running inside Studio One.

We map our "Knowledge Base" to the specific parameter IDs of Studio One version 6/6.5+.

5. Folder Structure

This is how the project repository will be organized on your hard drive.

/MixingMentor
  /Assets
    /Models          # The .onnx brain files (e.g., metalcore_v1.onnx)
    /Fonts           # UI Fonts
  /Docs              # This Bible
  /Python_Training   # The script that learns from Suno files
    /data            # Good/Bad audio folders
    train.py
  /Source            # The C++ Plugin Code
    /Core            # SessionManager, TrackProfile
    /DSP             # FeatureExtraction, Analysis
    /GUI             # GhostNeedleComponent, TextFeed
    PluginProcessor.cpp
    PluginEditor.cpp
  CMakeLists.txt
