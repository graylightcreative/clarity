# Source Code Libraries for NGN Clarity

# =============================================================================
# Core Library
# =============================================================================

add_library(ngn_clarity_core STATIC)

target_sources(ngn_clarity_core
    PRIVATE
        Core/PluginProcessor.cpp
        Core/PluginParameters.cpp
    PUBLIC FILE_SET HEADERS
        BASE_DIRS ${CMAKE_CURRENT_SOURCE_DIR}
        FILES
            Core/PluginProcessor.h
            Core/PluginParameters.h
            Core/Config.h
)

target_include_directories(ngn_clarity_core
    PUBLIC
        ${CMAKE_CURRENT_SOURCE_DIR}
)

target_link_libraries(ngn_clarity_core
    PUBLIC
        juce::juce_core
        juce::juce_audio_processors
)

# =============================================================================
# DSP Library
# =============================================================================

add_library(ngn_clarity_dsp STATIC)

target_sources(ngn_clarity_dsp
    PRIVATE
        DSP/AudioAnalyzer.cpp
        DSP/OnnxSessionManager.cpp
        DSP/InferencePipeline.cpp
        DSP/DSPAlgorithms.cpp
    PUBLIC FILE_SET HEADERS
        BASE_DIRS ${CMAKE_CURRENT_SOURCE_DIR}
        FILES
            DSP/AudioAnalyzer.h
            DSP/OnnxSessionManager.h
            DSP/InferencePipeline.h
            DSP/DSPAlgorithms.h
)

target_include_directories(ngn_clarity_dsp
    PUBLIC
        ${CMAKE_CURRENT_SOURCE_DIR}
        ${ONNX_RUNTIME_DIR}/include
)

target_link_directories(ngn_clarity_dsp
    PRIVATE
        ${ONNX_RUNTIME_DIR}/lib
)

target_link_libraries(ngn_clarity_dsp
    PUBLIC
        juce::juce_dsp
        juce::juce_audio_basics
    PRIVATE
        onnxruntime
)

# =============================================================================
# GUI Library
# =============================================================================

add_library(ngn_clarity_gui STATIC)

target_sources(ngn_clarity_gui
    PRIVATE
        GUI/EditorWindow.cpp
        GUI/SpectrumAnalyzer.cpp
        GUI/AdviceComponent.cpp
        GUI/LicenseDialog.cpp
        GUI/LookAndFeel.cpp
    PUBLIC FILE_SET HEADERS
        BASE_DIRS ${CMAKE_CURRENT_SOURCE_DIR}
        FILES
            GUI/EditorWindow.h
            GUI/SpectrumAnalyzer.h
            GUI/AdviceComponent.h
            GUI/LicenseDialog.h
            GUI/LookAndFeel.h
)

target_include_directories(ngn_clarity_gui
    PUBLIC
        ${CMAKE_CURRENT_SOURCE_DIR}
)

target_link_libraries(ngn_clarity_gui
    PUBLIC
        juce::juce_gui_basics
        juce::juce_gui_extra
        juce::juce_graphics
)

# =============================================================================
# Licensing Library (Vault Integration)
# =============================================================================

add_library(ngn_clarity_licensing STATIC)

target_sources(ngn_clarity_licensing
    PRIVATE
        Licensing/HMACKeyManager.cpp
        Licensing/ActivationClient.cpp
        Licensing/HardwareIDGenerator.cpp
        Licensing/LicenseManager.cpp
        Licensing/OfflineActivation.cpp
    PUBLIC FILE_SET HEADERS
        BASE_DIRS ${CMAKE_CURRENT_SOURCE_DIR}
        FILES
            Licensing/HMACKeyManager.h
            Licensing/ActivationClient.h
            Licensing/HardwareIDGenerator.h
            Licensing/LicenseManager.h
            Licensing/OfflineActivation.h
)

# Generate HMAC_Keys.h from template if secret is available
if(HMAC_CONFIGURED)
    # Include the secret key generation script
    # Note: This is prepared for PHASE_2A implementation
    message(STATUS "Preparing HMAC key injection for Licensing library")
    # include(${CMAKE_SOURCE_DIR}/CI/cmake/GenerateHMACKeys.cmake)
endif()

target_include_directories(ngn_clarity_licensing
    PUBLIC
        ${CMAKE_CURRENT_SOURCE_DIR}
        ${OPENSSL_INCLUDE_DIR}
        ${CURL_INCLUDE_DIRS}
)

target_link_libraries(ngn_clarity_licensing
    PUBLIC
        OpenSSL::Crypto
        CURL::libcurl
    PRIVATE
        ngn_clarity_utils
)

target_compile_definitions(ngn_clarity_licensing
    PRIVATE
        OPENSSL_USE_STATIC_LIBS
)

# =============================================================================
# Telemetry Library (Pulse Integration)
# =============================================================================

add_library(ngn_clarity_telemetry STATIC)

target_sources(ngn_clarity_telemetry
    PRIVATE
        Telemetry/PulseClient.cpp
        Telemetry/TelemetryData.cpp
        Telemetry/Anonymizer.cpp
        Telemetry/ConsentManager.cpp
    PUBLIC FILE_SET HEADERS
        BASE_DIRS ${CMAKE_CURRENT_SOURCE_DIR}
        FILES
            Telemetry/PulseClient.h
            Telemetry/TelemetryData.h
            Telemetry/Anonymizer.h
            Telemetry/ConsentManager.h
)

target_include_directories(ngn_clarity_telemetry
    PUBLIC
        ${CMAKE_CURRENT_SOURCE_DIR}
        ${CURL_INCLUDE_DIRS}
)

target_link_libraries(ngn_clarity_telemetry
    PUBLIC
        CURL::libcurl
    PRIVATE
        ngn_clarity_utils
)

# =============================================================================
# Utilities Library
# =============================================================================

add_library(ngn_clarity_utils STATIC)

target_sources(ngn_clarity_utils
    PRIVATE
        Utils/Logger.cpp
        Utils/Math.cpp
        Utils/Network.cpp
        Utils/StringUtils.cpp
    PUBLIC FILE_SET HEADERS
        BASE_DIRS ${CMAKE_CURRENT_SOURCE_DIR}
        FILES
            Utils/Logger.h
            Utils/Math.h
            Utils/Network.h
            Utils/StringUtils.h
)

target_include_directories(ngn_clarity_utils
    PUBLIC
        ${CMAKE_CURRENT_SOURCE_DIR}
)

target_link_libraries(ngn_clarity_utils
    PUBLIC
        juce::juce_core
)

# =============================================================================
# Main Plugin Entry Point
# =============================================================================

# Create a simple main source file for the plugin (will be created in PHASE_1)
# For now, this is a placeholder that will be properly implemented in PHASE_1

add_library(ngn_clarity_main OBJECT)

target_sources(ngn_clarity_main
    PRIVATE
        Main.cpp
)

target_include_directories(ngn_clarity_main
    PRIVATE
        ${CMAKE_CURRENT_SOURCE_DIR}
)

target_link_libraries(ngn_clarity_main
    PRIVATE
        juce::juce_core
)

# =============================================================================
# Summary
# =============================================================================

message(STATUS "Source libraries configured:")
message(STATUS "  ✓ ngn_clarity_core (Audio processor)")
message(STATUS "  ✓ ngn_clarity_dsp (Audio analysis & AI)")
message(STATUS "  ✓ ngn_clarity_gui (User interface)")
message(STATUS "  ✓ ngn_clarity_licensing (Vault HMAC)")
message(STATUS "  ✓ ngn_clarity_telemetry (Pulse integration)")
message(STATUS "  ✓ ngn_clarity_utils (Logging & utilities)")
