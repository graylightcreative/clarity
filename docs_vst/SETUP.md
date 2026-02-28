# NGN Clarity Development Setup Guide

**Complete setup instructions for developing NGN Clarity locally**

---

## Quick Start

### macOS
```bash
chmod +x setup.sh
./setup.sh
```

### Windows
```bash
setup.bat
```

### Linux
```bash
chmod +x setup.sh
./setup.sh
```

---

## Prerequisites

### Windows
- **Visual Studio 2019+** with C++ workload (Community, Professional, or Enterprise)
  - Download: https://visualstudio.microsoft.com/
  - During installation, select "Desktop development with C++"
- **CMake 3.25+**
  - Download: https://cmake.org/download/
  - Check "Add CMake to PATH" during installation
- **Git 2.30+**
  - Download: https://git-scm.com/download/win
  - Check "Add Git to PATH" during installation

### macOS
- **Xcode 13+** with Command Line Tools
  - Install: `xcode-select --install`
- **CMake 3.25+**
  - Install: `brew install cmake`
- **Git** (included with Xcode)
- **OpenSSL 1.1+** (for HMAC-SHA256)
  - Install: `brew install openssl`
- **libcurl** (for HTTP communication)
  - Install: `brew install curl`

### Linux (Debian/Ubuntu)
- **build-essential** (C++ compiler + tools)
  ```bash
  sudo apt-get install build-essential
  ```
- **CMake 3.25+**
  ```bash
  sudo apt-get install cmake
  ```
- **Git**
  ```bash
  sudo apt-get install git
  ```
- **Development headers**
  ```bash
  sudo apt-get install libssl-dev libcurl4-openssl-dev
  ```

---

## Manual Setup (Step-by-Step)

If you prefer not to use the automated setup scripts, follow these steps:

### 1. Clone Repository

```bash
git clone https://github.com/graylight/ngn-clarity.git
cd ngn-clarity
```

### 2. Create Build Directory

```bash
mkdir build
cd build
```

### 3. Configure with CMake

**macOS/Linux**:
```bash
cmake .. -DCMAKE_BUILD_TYPE=Release
```

**Windows**:
```batch
cmake .. -G "Visual Studio 17 2022" -DCMAKE_BUILD_TYPE=Release
```

### 4. Build Plugin

**macOS/Linux**:
```bash
cmake --build . --parallel 4
```

**Windows**:
```batch
cmake --build . --config Release --parallel 4
```

### 5. Locate Plugin

**macOS**:
```
build/plugins/VST3/NGN_Clarity.vst3
```

**Windows**:
```
build\plugins\VST3\NGN_Clarity.vst3
```

**Linux**:
```
build/plugins/VST3/NGN_Clarity.vst3
```

---

## Installing the Plugin

### macOS
```bash
cp -r build/plugins/VST3/NGN_Clarity.vst3 ~/Library/Audio/Plug-Ins/VST3/
```

### Windows
1. Open File Explorer
2. Navigate to: `C:\Program Files\Common Files\VST3\`
3. Copy `build\plugins\VST3\NGN_Clarity.vst3` to this folder

### Linux
```bash
mkdir -p ~/.vst3
cp -r build/plugins/VST3/NGN_Clarity.vst3 ~/.vst3/
```

---

## Verify Installation

1. **Open Studio One Pro 6.5+**
2. **Scan for plugins**:
   - Studio One → Preferences → Plugins → Plugin Manager
   - Click "Rescan all"
3. **Find the plugin**:
   - Category: Tools or Analyzers
   - Name: NGN Clarity
4. **Test the plugin**:
   - Create new blank song
   - Insert NGN Clarity on an audio track
   - You should see the plugin window

---

## IDE Setup

### VS Code (Recommended for macOS/Linux)

**Install extensions**:
1. C/C++ (Microsoft)
2. CMake Tools (Microsoft)
3. CMake (twxs)

**Open project**:
```bash
code .
```

**Configure CMake**:
1. Command Palette: `CMake: Configure`
2. Select compiler (clang++, g++, or MSVC)
3. Select build preset (Release)

**Build**:
- Command Palette: `CMake: Build`
- Or use sidebar: Build icon

### Visual Studio (Windows)

**Open project**:
1. File → Open → Folder
2. Select `ngn-clarity` folder
3. VS Code will open the CMake project

**Or use traditional approach**:
```bash
cd build
cmake .. -G "Visual Studio 17 2022"
start NGN_Clarity.sln
```

### CLion (All Platforms)

1. File → Open
2. Select `ngn-clarity` folder
3. CLion automatically opens CMakeLists.txt
4. Build & Run from toolbar

### Xcode (macOS)

```bash
cd build
cmake .. -G Xcode
open NGN_Clarity.xcodeproj
```

---

## Troubleshooting

### CMake Configuration Fails

**Error**: `CMake Error at CMakeLists.txt:XX: The C compiler "cc" is not able to compile`

**Solution**:
- macOS: `xcode-select --install`
- Windows: Install Visual Studio with C++
- Linux: `sudo apt-get install build-essential`

### Missing JUCE

**Error**: `Could not find JUCE framework`

**Solution**:
Set JUCE_PATH during configuration:
```bash
cmake .. -DJUCE_PATH=/path/to/JUCE -DCMAKE_BUILD_TYPE=Release
```

### Missing OpenSSL

**Error**: `Could not find OpenSSL`

**Solution**:
- macOS: `brew install openssl`
- Linux: `sudo apt-get install libssl-dev`
- Windows: Already bundled with Visual Studio

### Missing ONNX Runtime

**Error**: `ONNX Runtime not found`

**Solution**:
1. Download ONNX Runtime 1.18+ from: https://github.com/microsoft/onnxruntime/releases
2. Extract to: `third_party/onnxruntime-*/`
3. Reconfigure CMake: `cmake .. -DCMAKE_BUILD_TYPE=Release`

### Plugin Not Found After Installation

**Check**:
1. File is in correct VST3 directory (see above)
2. Run plugin scan in Studio One (Preferences → Plugins → Plugin Manager)
3. Check plugin is not blacklisted

**Verify file**:
- macOS: `ls -la ~/Library/Audio/Plug-Ins/VST3/NGN_Clarity.vst3`
- Windows: `dir "C:\Program Files\Common Files\VST3\NGN_Clarity.vst3"`

---

## Development Workflow

### 1. Make Code Changes

Edit files in `Source/` directory:
- `Source/Core/` - Audio processor
- `Source/DSP/` - Audio analysis
- `Source/GUI/` - User interface
- `Source/Licensing/` - Vault integration
- `Source/Telemetry/` - Pulse integration

### 2. Rebuild

```bash
cd build
cmake --build .
```

Or in IDE:
- VS Code: Command Palette → CMake: Build
- Visual Studio: Build → Build Solution
- CLion: Build → Build Project

### 3. Test

1. Reinstall plugin (copy new binary)
2. Reload in Studio One (if already open)
3. Test functionality

### 4. Debug

**Using VS Code**:
1. Add breakpoints (click left margin)
2. Command Palette: `CMake: Debug`
3. Use Debug sidebar

**Using Xcode/Visual Studio**:
1. Open generated project file
2. Set breakpoints
3. Run with debugger

---

## Git Workflow

### Initialize Repository

```bash
git init
git add .
git commit -m "Initial commit: PHASE_0 project setup"
git branch dev
```

### Create Feature Branch

```bash
git checkout -b feature/my-feature
# Make changes
git add .
git commit -m "Implement my feature"
git push origin feature/my-feature
# Create pull request on GitHub
```

### Keep in Sync

```bash
git fetch origin
git rebase origin/dev
```

---

## Build Configuration Options

### Release Build (Default)
```bash
cmake .. -DCMAKE_BUILD_TYPE=Release
```

Optimized for performance, no debug symbols.

### Debug Build
```bash
cmake .. -DCMAKE_BUILD_TYPE=Debug
```

Includes debug symbols, slower performance.

### Custom JUCE Path
```bash
cmake .. -DJUCE_PATH=/path/to/JUCE -DCMAKE_BUILD_TYPE=Release
```

### Vault HMAC Secret (PHASE_2A)

Set environment variable before building:
```bash
export GRAYLIGHT_HMAC_SECRET_KEY="64-char-hex-string-here"
cmake .. -DCMAKE_BUILD_TYPE=Release
```

---

## Troubleshooting Build Issues

### Clean Build

```bash
cd build
rm -rf *
cmake .. -DCMAKE_BUILD_TYPE=Release
cmake --build . --parallel 4
```

### Update Dependencies

```bash
# macOS
brew upgrade cmake openssl libcurl

# Linux
sudo apt-get update
sudo apt-get upgrade cmake libssl-dev libcurl4-openssl-dev
```

### Check CMake Version

```bash
cmake --version
# Should be 3.25.0 or higher
```

---

## Documentation

- **README.md** - Project overview
- **CHANGELOG.md** - Version history
- **docs/bible/00 - Index.md** - Architecture navigation
- **PHASE_0_CLI_AGENT_PLAN.md** - Development roadmap
- **docs/SETUP.md** - This file

---

## Support

**Issues**:
- GitHub Issues: https://github.com/graylight/ngn-clarity/issues
- Include error messages and platform info

**Discussions**:
- GitHub Discussions: https://github.com/graylight/ngn-clarity/discussions

**Email**:
- support@graylightcreative.com

---

**Last Updated**: February 11, 2025
**Status**: PHASE_0 Complete
**Maintained By**: Graylight Creative Engineering Team
