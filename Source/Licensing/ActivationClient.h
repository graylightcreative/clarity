#pragma once

#include "HMACKeyManager.h"
#include <string>
#include <memory>
#include <cstdint>
#include <nlohmann/json.hpp>

using json = nlohmann::json;

namespace Licensing {

/**
 * ActivationClient
 *
 * Handles online license activation requests to the NGN Clarity backend.
 * Automatically signs all requests with HMAC-SHA256 signatures using the
 * embedded Vault secret key.
 *
 * Request Flow:
 * 1. Build JSON payload (hardware_id, license_key, timestamp, action)
 * 2. Compute HMAC-SHA256 signature
 * 3. Send POST request with signature in X-Graylight-Signature header
 * 4. Parse response and validate activation
 *
 * See: docs/bible/12 - NGN Clarity API Specifications (Online Activation Request Format)
 */
class ActivationClient {
public:
    struct ActivationResponse {
        bool success = false;
        std::string status;           // "activated", "trial", "invalid", "error"
        std::string license_key;
        std::string email;
        std::string expiration_date;  // ISO 8601 format
        int machine_limit = 0;
        int machines_activated = 0;
        std::string error_message;
        std::string error_code;       // "invalid_signature", "expired_timestamp", etc.

        // Raw response for debugging
        json raw_response;
    };

    ActivationClient();
    ~ActivationClient() = default;

    // Deleted copy operations
    ActivationClient(const ActivationClient&) = delete;
    ActivationClient& operator=(const ActivationClient&) = delete;

    /**
     * Activate a license on this machine
     *
     * @param hardware_id The machine fingerprint (from HardwareIDGenerator)
     * @param license_key The license key from user purchase email
     * @param backend_url The backend activation endpoint (e.g., "https://api.example.com/licenses/activate")
     *
     * @return ActivationResponse with status and error details
     *
     * This method:
     * 1. Gets current UNIX timestamp
     * 2. Builds JSON payload
     * 3. Signs with HMAC-SHA256
     * 4. Sends HTTP POST request
     * 5. Validates response signature
     * 6. Parses license details
     */
    ActivationResponse activateLicense(
        const std::string& hardware_id,
        const std::string& license_key,
        const std::string& backend_url
    );

    /**
     * Validate an existing license (check expiration, machine limit)
     */
    ActivationResponse validateLicense(
        const std::string& hardware_id,
        const std::string& license_key,
        const std::string& backend_url
    );

    /**
     * Deactivate this machine from a license
     */
    ActivationResponse deactivateLicense(
        const std::string& hardware_id,
        const std::string& license_key,
        const std::string& backend_url
    );

    /**
     * Set network timeout for activation requests (milliseconds)
     * Default: 10000 ms (10 seconds)
     */
    void setNetworkTimeout(uint32_t timeout_ms) {
        network_timeout_ms_ = timeout_ms;
    }

    /**
     * Check if the client is properly initialized
     */
    bool isValid() const { return hmac_manager_ && hmac_manager_->isValid(); }

private:
    std::unique_ptr<HMACKeyManager> hmac_manager_;
    uint32_t network_timeout_ms_ = 10000;

    /**
     * Build the activation request JSON payload
     *
     * @param hardware_id The machine fingerprint
     * @param license_key The license key
     * @param action "activate", "validate", or "deactivate"
     * @param timestamp UNIX timestamp (seconds)
     *
     * @return JSON payload as string (compact, no whitespace)
     */
    std::string buildPayload(
        const std::string& hardware_id,
        const std::string& license_key,
        const std::string& action,
        uint64_t timestamp
    ) const;

    /**
     * Execute HTTP POST request with HMAC signature
     *
     * @param url The backend endpoint URL
     * @param payload The JSON payload (will be signed)
     *
     * @return Raw HTTP response body
     */
    std::string performHTTPRequest(
        const std::string& url,
        const std::string& payload
    ) const;

    /**
     * Parse activation response from backend
     */
    ActivationResponse parseResponse(const std::string& response_body) const;

    /**
     * Get current UNIX timestamp (seconds since epoch)
     */
    static uint64_t getCurrentTimestamp();

    /**
     * Check if a timestamp is too old (replay attack prevention)
     * Rejects timestamps older than 5 minutes
     *
     * @param timestamp The timestamp to check
     * @return true if valid, false if too old
     */
    static bool isTimestampValid(uint64_t timestamp);
};

} // namespace Licensing
