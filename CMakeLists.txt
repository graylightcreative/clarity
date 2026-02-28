cmake_minimum_required(VERSION 3.25)

# NGN Clarity - AI-Powered Mixing Mentor VST3 Plugin
project(NGN_Clarity
    VERSION 0.1.0
    DESCRIPTION "A VST3 plugin that analyzes mixing tracks and teaches users how to fix them"
    LANGUAGES CXX)

# =============================================================================
# C++ Standard Configuration
# =============================================================================

set(CMAKE_CXX_STANDARD 17)
set(CMAKE_CXX_STANDARD_REQUIRED ON)
set(CMAKE_CXX_EXTENSIONS OFF)

# Compiler-specific settings
if(MSVC)
    # Visual Studio
    add_compile_options(/W4 /WX)  # All warnings, warnings as errors
    add_compile_definitions(_CRT_SECURE_NO_WARNINGS)
else()
    # GCC/Clang
    add_compile_options(-Wall -Wextra -Wpedantic -Werror)
endif()

# =============================================================================
# Project Version
# =============================================================================

set(PLUGIN_VERSION_MAJOR 0)
set(PLUGIN_VERSION_MINOR 1)
set(PLUGIN_VERSION_PATCH 0)
set(PLUGIN_VERSION "${PLUGIN_VERSION_MAJOR}.${PLUGIN_VERSION_MINOR}.${PLUGIN_VERSION_PATCH}")

# Git information
execute_process(
    COMMAND git rev-parse --short HEAD
    WORKING_DIRECTORY "${CMAKE_SOURCE_DIR}"
    OUTPUT_VARIABLE GIT_COMMIT_HASH
    OUTPUT_STRIP_TRAILING_WHITESPACE
)
if(NOT GIT_COMMIT_HASH)
    set(GIT_COMMIT_HASH "unknown")
endif()

# =============================================================================
# Output Directories
# =============================================================================

set(CMAKE_RUNTIME_OUTPUT_DIRECTORY "${CMAKE_BINARY_DIR}/bin")
set(CMAKE_LIBRARY_OUTPUT_DIRECTORY "${CMAKE_BINARY_DIR}/lib")
set(CMAKE_ARCHIVE_OUTPUT_DIRECTORY "${CMAKE_BINARY_DIR}/lib")

# Plugin-specific output
set(PLUGIN_OUTPUT_DIR "${CMAKE_BINARY_DIR}/plugins")

# =============================================================================
# Find & Configure Required Packages
# =============================================================================

# JUCE Framework
message(STATUS "Configuring JUCE framework...")
set(JUCE_FORMATS VST3)
set(JUCE_ENABLE_MODULE_SOURCE_GROUPS ON)

# Note: JUCE should be added as a subdirectory or fetched
# For now, we assume it's available at JUCE_PATH
if(NOT DEFINED JUCE_PATH)
    # Try to find JUCE in common locations
    find_path(JUCE_PATH
        NAMES CMakeLists.txt
        PATHS "$ENV{HOME}/JUCE" "/opt/JUCE" "C:/JUCE"
        NO_DEFAULT_PATH
    )
    if(NOT JUCE_PATH)
        message(FATAL_ERROR "JUCE not found. Please set JUCE_PATH or install JUCE to common location")
    endif()
endif()

message(STATUS "JUCE_PATH: ${JUCE_PATH}")
add_subdirectory(${JUCE_PATH} juce)

# =============================================================================
# OpenSSL (for HMAC-SHA256)
# =============================================================================

message(STATUS "Finding OpenSSL for HMAC-SHA256 cryptography...")
find_package(OpenSSL 1.1 REQUIRED)
if(OPENSSL_FOUND)
    message(STATUS "OpenSSL found: ${OPENSSL_INCLUDE_DIR}")
else()
    message(FATAL_ERROR "OpenSSL 1.1+ required for HMAC-SHA256 implementation")
endif()

# =============================================================================
# libcurl (for API communication)
# =============================================================================

message(STATUS "Finding libcurl for API communication...")
find_package(CURL REQUIRED)
if(CURL_FOUND)
    message(STATUS "libcurl found: ${CURL_INCLUDE_DIRS}")
else()
    message(FATAL_ERROR "libcurl required for Nexus/Ledger API communication")
endif()

# =============================================================================
# ONNX Runtime (for AI inference)
# =============================================================================

message(STATUS "Configuring ONNX Runtime for AI inference...")

if(WIN32)
    # Windows x64
    set(ONNX_RUNTIME_DIR "${CMAKE_SOURCE_DIR}/third_party/onnxruntime-win-x64-1.18.0"
        CACHE PATH "ONNX Runtime directory (Windows)")

    if(NOT EXISTS "${ONNX_RUNTIME_DIR}")
        message(WARNING "ONNX Runtime not found at ${ONNX_RUNTIME_DIR}")
        message("Please download from: https://github.com/microsoft/onnxruntime/releases")
        message("And extract to: ${ONNX_RUNTIME_DIR}")
    endif()

elseif(APPLE)
    # macOS (universal - Intel + Apple Silicon)
    set(ONNX_RUNTIME_DIR "${CMAKE_SOURCE_DIR}/third_party/onnxruntime-osx-universal-1.18.0"
        CACHE PATH "ONNX Runtime directory (macOS)")

    if(NOT EXISTS "${ONNX_RUNTIME_DIR}")
        message(WARNING "ONNX Runtime not found at ${ONNX_RUNTIME_DIR}")
        message("Please download from: https://github.com/microsoft/onnxruntime/releases")
        message("And extract to: ${ONNX_RUNTIME_DIR}")
    endif()
endif()

# =============================================================================
# Vault HMAC Secret Injection (PHASE_2A)
# =============================================================================

message(STATUS "Checking for Vault HMAC secret injection...")

if(DEFINED ENV{GRAYLIGHT_HMAC_SECRET_KEY})
    message(STATUS "✓ GRAYLIGHT_HMAC_SECRET_KEY found in environment (from GitHub Actions)")

    # Generate random XOR obfuscation key (changes per build)
    string(RANDOM LENGTH 1 ALPHABET "0123456789abcdef" XOR_KEY_HEX)
    string(HEX_ENCODE_RANDOM XOR_KEY_HEX "${XOR_KEY_HEX}")

    # CMake script to generate HMAC_Keys.h from template
    # This will be implemented in CI/cmake/GenerateHMACKeys.cmake during PHASE_2A
    set(HMAC_CONFIGURED TRUE)

    message(STATUS "HMAC key will be injected at build-time")
    message(STATUS "Generated obfuscation key: ${XOR_KEY_HEX}")

else()
    message(WARNING "GRAYLIGHT_HMAC_SECRET_KEY not set in environment")
    message("PHASE_2A licensing features will not be available")
    message("To enable: Set GRAYLIGHT_HMAC_SECRET_KEY environment variable")
    set(HMAC_CONFIGURED FALSE)
endif()

# =============================================================================
# Add Subdirectories
# =============================================================================

message(STATUS "Adding source subdirectories...")
add_subdirectory(Source)

# Tests are optional
if(CMAKE_BUILD_TYPE MATCHES Debug)
    message(STATUS "Adding test directory...")
    enable_testing()
    # add_subdirectory(Tests)  # Uncomment when Tests are ready
endif()

# =============================================================================
# Plugin Target (JUCE)
# =============================================================================

message(STATUS "Creating NGN Clarity VST3 plugin target...")

juce_add_plugin(NGN_Clarity
    # Product info
    COMPANY_NAME "Graylight Creative"
    COMPANY_WEBSITE "https://graylightcreative.com"
    COMPANY_EMAIL "support@graylightcreative.com"

    # Plugin metadata
    PLUGIN_MANUFACTURER_CODE "GRAY"
    PLUGIN_CODE "NGN1"
    FORMATS VST3
    PRODUCT_NAME "NGN Clarity"
    DESCRIPTION "AI-powered mixing mentor plugin"

    # Version
    VERSION "${PLUGIN_VERSION}"
    PLUGIN_VERSION_CODE 000100

    # Features
    VST3_CATEGORIES "Fx|Analyzer"
    AU_MAIN_TYPE "kAudioUnitType_Effect"

    # Plugin options
    NEEDS_MIDI_OUTPUT FALSE
    NEEDS_MIDI_INPUT FALSE
    IS_SYNTH FALSE
    EDITOR_WANTS_KEYBOARD_FOCUS FALSE
    COPY_PLUGIN_AFTER_BUILD FALSE
)

# Set output directory for plugin
set_target_properties(NGN_Clarity PROPERTIES
    LIBRARY_OUTPUT_DIRECTORY "${PLUGIN_OUTPUT_DIR}/VST3"
    RUNTIME_OUTPUT_DIRECTORY "${PLUGIN_OUTPUT_DIR}/VST3"
)

# =============================================================================
# Link Libraries to Plugin Target
# =============================================================================

target_link_libraries(NGN_Clarity
    PRIVATE
        # JUCE libraries
        juce::juce_core
        juce::juce_audio_basics
        juce::juce_audio_devices
        juce::juce_audio_formats
        juce::juce_audio_processors
        juce::juce_dsp
        juce::juce_gui_basics
        juce::juce_gui_extra
        juce::juce_graphics

        # Security & Cryptography
        OpenSSL::Crypto
        OpenSSL::SSL

        # Networking
        CURL::libcurl

        # Source code libraries
        ngn_clarity_core
        ngn_clarity_dsp
        ngn_clarity_gui
        ngn_clarity_licensing
        ngn_clarity_telemetry
        ngn_clarity_utils
)

# =============================================================================
# Compiler Definitions
# =============================================================================

target_compile_definitions(NGN_Clarity
    PRIVATE
        # Plugin info
        NGN_VERSION_STRING="${PLUGIN_VERSION}"
        NGN_VERSION_MAJOR=${PLUGIN_VERSION_MAJOR}
        NGN_VERSION_MINOR=${PLUGIN_VERSION_MINOR}
        NGN_VERSION_PATCH=${PLUGIN_VERSION_PATCH}
        NGN_GIT_COMMIT="${GIT_COMMIT_HASH}"

        # Feature flags
        $<$<CONFIG:Debug>:NGN_DEBUG=1>
        $<$<CONFIG:Release>:NGN_RELEASE=1>

        # Platform detection
        $<$<PLATFORM_ID:Windows>:NGN_WINDOWS=1>
        $<$<PLATFORM_ID:Darwin>:NGN_MACOS=1>
        $<$<PLATFORM_ID:Linux>:NGN_LINUX=1>

        # HMAC configuration
        $<$<BOOL:${HMAC_CONFIGURED}>:NGN_HMAC_CONFIGURED=1>
)

# =============================================================================
# Installation & Distribution
# =============================================================================

message(STATUS "Configuring plugin installation...")

# Install plugin to system locations (optional)
if(APPLE)
    # macOS: ~/Library/Audio/Plug-Ins/VST3/
    set(VST3_INSTALL_DIR "~/Library/Audio/Plug-Ins/VST3")
elseif(WIN32)
    # Windows: C:\Program Files\Common Files\VST3\
    set(VST3_INSTALL_DIR "C:/Program Files/Common Files/VST3")
endif()

# Note: Installation will be handled by CI/CD pipeline and installers (PHASE_10)

# =============================================================================
# Summary
# =============================================================================

message(STATUS "")
message(STATUS "=== NGN Clarity Build Configuration ===")
message(STATUS "Version: ${PLUGIN_VERSION}")
message(STATUS "Git commit: ${GIT_COMMIT_HASH}")
message(STATUS "Build type: ${CMAKE_BUILD_TYPE}")
message(STATUS "C++ Standard: ${CMAKE_CXX_STANDARD}")
message(STATUS "")
message(STATUS "Components:")
message(STATUS "  ✓ JUCE: ${JUCE_PATH}")
message(STATUS "  ✓ OpenSSL: ${OPENSSL_INCLUDE_DIR}")
message(STATUS "  ✓ libcurl: ${CURL_INCLUDE_DIRS}")
message(STATUS "  ${ONNX_STATUS}")
message(STATUS "")
message(STATUS "Output:")
message(STATUS "  Plugin: ${PLUGIN_OUTPUT_DIR}/VST3/")
message(STATUS "  Build: ${CMAKE_BINARY_DIR}/")
message(STATUS "")
message(STATUS "Next: cmake --build . to compile the plugin")
message(STATUS "====================================")
message(STATUS "")

# =============================================================================
# Compiler Warnings & Features (Advanced)
# =============================================================================

# Enable LTO (Link Time Optimization) for Release builds
if(CMAKE_BUILD_TYPE MATCHES Release)
    if(MSVC)
        target_compile_options(NGN_Clarity PRIVATE /GL)
        target_link_options(NGN_Clarity PRIVATE /LTCG)
    else()
        include(CheckIPOSupported)
        check_ipo_supported(RESULT ipo_supported OUTPUT ipo_output)
        if(ipo_supported)
            set_target_properties(NGN_Clarity PROPERTIES INTERPROCEDURAL_OPTIMIZATION TRUE)
        endif()
    endif()
endif()
