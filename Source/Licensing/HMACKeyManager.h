#pragma once

#include <string>
#include <vector>
#include <memory>
#include <cstdint>

namespace Licensing {

/**
 * HMACKeyManager
 *
 * Manages de-obfuscation of the embedded HMAC-SHA256 secret key and provides
 * cryptographic signing operations. The key is compiled into the binary as an
 * obfuscated byte array to prevent easy memory scraping.
 *
 * Thread-safe: Can be safely used from multiple threads.
 * Secure: Clears key material from memory on destruction.
 */
class HMACKeyManager {
public:
    HMACKeyManager();
    ~HMACKeyManager();

    // Deleted copy operations to prevent accidental key duplication
    HMACKeyManager(const HMACKeyManager&) = delete;
    HMACKeyManager& operator=(const HMACKeyManager&) = delete;

    // Move operations allowed (responsibility transferred)
    HMACKeyManager(HMACKeyManager&&) noexcept = default;
    HMACKeyManager& operator=(HMACKeyManager&&) noexcept = default;

    /**
     * Compute HMAC-SHA256 of a message using the embedded secret key
     *
     * @param message The input message to sign (typically JSON string)
     * @return Hex-encoded HMAC-SHA256 digest (64 characters)
     *
     * Example:
     *   HMACKeyManager mgr;
     *   std::string signature = mgr.computeHMAC(R"({"hardware_id":"ABC123","timestamp":1707561000})");
     *   // Returns: "a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b"
     */
    std::string computeHMAC(const std::string& message) const;

    /**
     * Verify an HMAC signature against a message
     *
     * @param message The original message
     * @param signature The hex-encoded signature to verify
     * @return true if signature is valid, false otherwise
     *
     * Note: Uses constant-time comparison to prevent timing attacks
     */
    bool verifyHMAC(const std::string& message, const std::string& signature) const;

    /**
     * Get the de-obfuscated key length (always 32 bytes for SHA256)
     */
    size_t getKeyLength() const { return deobfuscated_key_.size(); }

    /**
     * Check if the key was successfully de-obfuscated
     */
    bool isValid() const { return !deobfuscated_key_.empty(); }

private:
    std::vector<uint8_t> deobfuscated_key_;

    /**
     * De-obfuscate the embedded key by applying XOR with the compile-time key
     * Called once during construction
     */
    void deobfuscateKey();

    /**
     * Internal constant-time comparison for signatures
     * Prevents timing attacks that could leak information about valid signatures
     */
    static bool constantTimeCompare(const std::string& a, const std::string& b);
};

} // namespace Licensing
