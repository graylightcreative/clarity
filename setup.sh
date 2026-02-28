#!/bin/bash

# =============================================================================
# NGN Clarity Development Setup Script (macOS & Linux)
#
# This script automates the setup of a development environment for NGN Clarity.
# It checks requirements, installs dependencies, and configures the build.
#
# Usage:
#   chmod +x setup.sh
#   ./setup.sh
#
# =============================================================================

set -e  # Exit on first error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# =============================================================================
# Helper Functions
# =============================================================================

print_header() {
    echo -e "${BLUE}=== $1 ===${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

check_command() {
    if command -v "$1" &> /dev/null; then
        print_success "$1 found"
        return 0
    else
        print_error "$1 not found"
        return 1
    fi
}

# =============================================================================
# Introduction
# =============================================================================

echo ""
print_header "NGN Clarity Development Environment Setup"
echo ""
echo "This script will:"
echo "  1. Verify required tools are installed"
echo "  2. Install missing dependencies (with your permission)"
echo "  3. Configure the build system"
echo "  4. Prepare the development environment"
echo ""
echo "Platform: $(uname -s)"
echo ""

# =============================================================================
# Check Requirements
# =============================================================================

print_header "Checking Requirements"
echo ""

REQUIREMENTS_MET=true

# Git
if ! check_command "git"; then
    REQUIREMENTS_MET=false
    print_error "Git is required. Please install from https://git-scm.com/"
fi

# CMake 3.25+
if ! check_command "cmake"; then
    REQUIREMENTS_MET=false
    echo "CMake is required. Install with:"
    echo "  macOS: brew install cmake"
    echo "  Linux: sudo apt-get install cmake"
else
    CMAKE_VERSION=$(cmake --version | head -n1 | awk '{print $3}')
    echo "  Version: $CMAKE_VERSION"
fi

# C++ Compiler
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    if ! check_command "clang++"; then
        REQUIREMENTS_MET=false
        print_error "Xcode Command Line Tools required"
        echo "Install with: xcode-select --install"
    else
        CLANG_VERSION=$(clang++ --version | head -n1)
        echo "  $CLANG_VERSION"
    fi
else
    # Linux
    if ! check_command "g++"; then
        REQUIREMENTS_MET=false
        print_error "g++ compiler required"
        echo "Install with: sudo apt-get install build-essential"
    else
        GCC_VERSION=$(g++ --version | head -n1)
        echo "  $GCC_VERSION"
    fi
fi

echo ""

# =============================================================================
# Install Dependencies
# =============================================================================

print_header "Installing Dependencies"
echo ""

if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS with Homebrew
    print_warning "macOS detected - using Homebrew for dependency management"
    echo ""

    if ! command -v "brew" &> /dev/null; then
        print_error "Homebrew not found. Install from https://brew.sh/"
        exit 1
    fi

    echo "Installing required packages..."
    echo ""

    # OpenSSL (for HMAC-SHA256)
    if ! check_command "openssl"; then
        echo "Installing OpenSSL..."
        brew install openssl
    fi

    # libcurl (for API communication)
    if ! check_command "curl"; then
        echo "Installing libcurl..."
        brew install libcurl
    fi

    print_success "Dependencies installed"

elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    # Linux (Debian/Ubuntu)
    print_warning "Linux detected - using apt for dependency management"
    echo ""

    echo "Installing required packages (requires sudo)..."
    echo ""

    sudo apt-get update
    sudo apt-get install -y \
        build-essential \
        cmake \
        git \
        libssl-dev \
        libcurl4-openssl-dev

    print_success "Dependencies installed"

else
    print_error "Unsupported platform: $OSTYPE"
    exit 1
fi

echo ""

# =============================================================================
# Create Build Directory
# =============================================================================

print_header "Configuring Build System"
echo ""

if [ -d "build" ]; then
    print_warning "build/ directory already exists"
    read -p "Delete and recreate? (y/N) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        rm -rf build
        print_success "Deleted build/"
    fi
fi

if [ ! -d "build" ]; then
    mkdir -p build
    print_success "Created build/"
fi

cd build

echo ""

# =============================================================================
# CMake Configuration
# =============================================================================

print_header "Running CMake Configuration"
echo ""

CMAKE_FLAGS="-DCMAKE_BUILD_TYPE=Release"

# Add platform-specific flags
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS: universal binary (Intel + Apple Silicon)
    CMAKE_FLAGS="$CMAKE_FLAGS -DCMAKE_OSX_ARCHITECTURES=arm64;x86_64"
    echo "macOS detected - building universal binary"
fi

echo "CMake command: cmake .. $CMAKE_FLAGS"
echo ""

if cmake .. $CMAKE_FLAGS; then
    print_success "CMake configuration completed"
else
    print_error "CMake configuration failed"
    print_warning "Check error messages above"
    exit 1
fi

echo ""

# =============================================================================
# Build Plugin
# =============================================================================

print_header "Building NGN Clarity Plugin"
echo ""

echo "Starting build... (this may take a few minutes)"
echo ""

if cmake --build . --parallel 4; then
    print_success "Build completed successfully"
else
    print_error "Build failed"
    print_warning "Check error messages above"
    exit 1
fi

echo ""

# =============================================================================
# Build Output
# =============================================================================

print_header "Build Results"
echo ""

PLUGIN_PATH="$(pwd)/plugins/VST3/NGN_Clarity.vst3"

if [ -d "$PLUGIN_PATH" ]; then
    print_success "Plugin built successfully"
    echo "Location: $PLUGIN_PATH"
    echo ""
    echo "Next steps:"
    if [[ "$OSTYPE" == "darwin"* ]]; then
        echo "  1. Copy to ~/Library/Audio/Plug-Ins/VST3/"
        echo "     cp -r \"$PLUGIN_PATH\" ~/Library/Audio/Plug-Ins/VST3/"
    else
        echo "  1. Copy to ~/.vst3/"
        echo "     cp -r \"$PLUGIN_PATH\" ~/.vst3/"
    fi
    echo "  2. Open Studio One Pro"
    echo "  3. Scan for plugins"
    echo "  4. Find 'NGN Clarity' in the plugin list"
else
    print_error "Plugin not found at expected location"
    echo "Check build output above for errors"
    exit 1
fi

echo ""

# =============================================================================
# IDE Setup (Optional)
# =============================================================================

print_header "IDE Setup (Optional)"
echo ""

echo "For development, you can use:"
echo ""
echo "1. VS Code (lightweight, free)"
echo "   https://code.visualstudio.com/"
echo "   Extensions: C/C++, CMake Tools"
echo ""
echo "2. CLion (full-featured, paid)"
echo "   https://www.jetbrains.com/clion/"
echo "   Opens CMakeLists.txt directly"
echo ""
echo "3. Xcode (macOS only)"
echo "   cmake .. -G Xcode"
echo ""

# =============================================================================
# Documentation
# =============================================================================

print_header "Documentation"
echo ""

echo "Read the following to get started:"
echo ""
echo "  • README.md - Project overview"
echo "  • docs/SETUP.md - Detailed setup instructions"
echo "  • docs/bible/00 - Index.md - Architecture documentation"
echo "  • PHASE_0_CLI_AGENT_PLAN.md - Development roadmap"
echo ""

# =============================================================================
# Final Message
# =============================================================================

echo ""
print_success "Setup complete!"
echo ""
echo "Next steps:"
echo "  1. Review documentation"
echo "  2. Copy plugin to system VST3 folder"
echo "  3. Open Studio One Pro and scan plugins"
echo "  4. Start PHASE_1 development"
echo ""

echo "For questions, see docs/SETUP.md or create an issue on GitHub"
echo ""
