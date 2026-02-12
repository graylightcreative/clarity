02 Data Structures

Project: The Mixing Mentor
Module: Data Architecture
Status: Defined

1. The Core Models

This chapter defines the C++ structs and classes that hold the state of the mix. These structures are shared between the audio processing thread (Real-time) and the GUI thread (Visualization).

1.1 The Track Profile (TrackProfile)

This is the fundamental unit of the system. Every instance of the plugin creates one TrackProfile. This is the "Identity Card" for a specific instance.

struct TrackProfile {
    // Identity
    juce::Uuid instanceID;      // Unique ID for this plugin instance
    juce::String trackName;     // e.g., "Kick Drum" (pulled from host if possible, or user set)
    
    // Context
    InstrumentType instrument;  // Enum: Kick, Snare, Bass, Guitar_Elec, Vocal_Lead...
    GenreTarget genre;          // Enum: Metalcore, Rock_Hard, Pop_Punk...
    
    // The "Fingerprint" (The Analysis Data)
    AudioFingerprint fingerprint;
    
    // State
    bool isCommitted;           // True if user has "locked" this track
    bool isMuted;               // Helper for analysis logic (don't analyze muted tracks)
    double sampleRate;
};


1.2 The Audio Fingerprint (AudioFingerprint)

To save CPU, we do not store raw audio. We store a mathematical reduction of the audio characteristics. This is what is passed to the SessionManager.

struct AudioFingerprint {
    // Frequency Domain (Averaged over 3-5 seconds)
    // We use a 32-band approximation for masking checks (low CPU vs 1024-bin FFT)
    std::array<float, 32> spectralEnergy; 
    
    // Dynamics
    float peakLUFS;             // Loudness standard
    float dynamicRange;         // Difference between Peak and RMS
    float transientAttackTime;  // Measured in ms (How "punchy" is it?)
    float sustainLevel;         // Measured in dB (How "ringy" is it?)
    
    // Issues
    bool hasClipping;           // Digital overs detected
    bool hasPhaseIssues;        // Correlation < 0
};


2. The Global Engine

2.1 The Session Manager (SessionManager)

This is the Singleton that acts as the "Hive Mind." It is thread-safe to allow multiple plugin instances to read/write simultaneously.

class SessionManager {
public:
    // Accessor
    static SessionManager& getInstance();

    // Registry Methods
    void registerTrack(const TrackProfile& profile);
    void updateFingerprint(const juce::Uuid& id, const AudioFingerprint& newPrint);
    void removeTrack(const juce::Uuid& id);

    // Context Queries (The "Accumulator" Logic)
    std::vector<TrackProfile> getTracksByInstrument(InstrumentType type);
    bool hasFoundation(); // Returns true if Kick + Snare exist
    
    // Conflict Checks
    // Returns a list of IDs that are clashing with the 'querier'
    std::vector<MaskingReport> checkMasking(const TrackProfile& querier);

private:
    std::vector<TrackProfile> sessionTracks;
    juce::ReadWriteLock lock; // Essential for thread safety
};


3. The Knowledge Base Models

3.1 Genre Targets (GenreTargetProfile)

This structure holds the "Ideal" values derived from our Suno AI training. The AnalysisEngine compares the user's AudioFingerprint against this target.

struct GenreTargetProfile {
    GenreTarget id; // e.g., Metalcore
    
    // Ideal Targets
    std::array<float, 32> idealSpectrum; // The "Gold Standard" EQ curve
    float idealDynamicRange;             // e.g., 6dB for squashed Metal Snare
    float idealAttackTime;               // e.g., < 10ms for Clicky Kick
    
    // Tolerances (How strict is the Mentor?)
    float spectrumTolerance; // +/- 3dB
};


3.2 The Mapping Dictionary (PluginMap)

Used to translate advice into specific DAW parameters. This allows the AI to say "Ratio 4:1" and have the code know that equals 0.65 on the Studio One Compressor knob.

struct DAWPluginMap {
    String dawName;          // "Studio One"
    String pluginName;       // "Compressor"
    
    // Map generic terms to 0.0-1.0 float values
    float ratioKnobID;       // Parameter Index
    float thresholdKnobID;   
    
    // Helper to translate real values to normalized floats
    // e.g., 4:1 Ratio -> 0.65 normalized value
    std::function<float(float)> ratioToFloat; 
};


4. Implementation Notes

Thread Safety: The SessionManager must use a ReadWriteLock. Multiple tracks will read (Analysis) often, but write (Update Fingerprint) less often.

Data Size: The AudioFingerprint is extremely small (< 1KB). This ensures that even with 100 tracks, the "Hive Mind" memory footprint is negligible.

Update Rate: Passive tracks do not update the SessionManager. Only Active tracks push updates.