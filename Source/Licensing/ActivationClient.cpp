#include "ActivationClient.h"
#include <chrono>
#include <sstream>
#include <curl/curl.h>

namespace Licensing {

ActivationClient::ActivationClient() {
    try {
        hmac_manager_ = std::make_unique<HMACKeyManager>();
    } catch (const std::exception& e) {
        // Log error: HMACKeyManager initialization failed
        // In production, this would log to proper error handler
        hmac_manager_ = nullptr;
    }
}

uint64_t ActivationClient::getCurrentTimestamp() {
    auto now = std::chrono::system_clock::now();
    auto duration = now.time_since_epoch();
    return std::chrono::duration_cast<std::chrono::seconds>(duration).count();
}

bool ActivationClient::isTimestampValid(uint64_t timestamp) {
    uint64_t now = getCurrentTimestamp();
    uint64_t five_minutes = 300;

    // Reject if timestamp is older than 5 minutes
    if (now > timestamp && (now - timestamp) > five_minutes) {
        return false;
    }

    // Also reject if timestamp is in the future (allow 1 minute clock skew)
    if (timestamp > now && (timestamp - now) > 60) {
        return false;
    }

    return true;
}

std::string ActivationClient::buildPayload(
    const std::string& hardware_id,
    const std::string& license_key,
    const std::string& action,
    uint64_t timestamp
) const {
    json payload = {
        {"hardware_id", hardware_id},
        {"license_key", license_key},
        {"timestamp", timestamp},
        {"action", action}
    };

    // Return compact JSON (no whitespace) for signature
    return payload.dump();
}

ActivationClient::ActivationResponse ActivationClient::activateLicense(
    const std::string& hardware_id,
    const std::string& license_key,
    const std::string& backend_url
) {
    ActivationResponse response;

    if (!isValid()) {
        response.success = false;
        response.status = "error";
        response.error_code = "hmac_key_not_initialized";
        response.error_message = "HMAC key manager not initialized";
        return response;
    }

    try {
        uint64_t timestamp = getCurrentTimestamp();
        std::string payload = buildPayload(hardware_id, license_key, "activate", timestamp);

        // Sign the payload
        std::string signature = hmac_manager_->computeHMAC(payload);

        // Send request (implementation depends on HTTP library)
        std::string response_body = performHTTPRequest(backend_url, payload);

        // Parse response
        response = parseResponse(response_body);

        if (response.success) {
            response.status = "activated";
        }

    } catch (const std::exception& e) {
        response.success = false;
        response.status = "error";
        response.error_code = "activation_failed";
        response.error_message = std::string("Activation error: ") + e.what();
    }

    return response;
}

ActivationClient::ActivationResponse ActivationClient::validateLicense(
    const std::string& hardware_id,
    const std::string& license_key,
    const std::string& backend_url
) {
    ActivationResponse response;

    if (!isValid()) {
        response.success = false;
        response.status = "error";
        response.error_code = "hmac_key_not_initialized";
        response.error_message = "HMAC key manager not initialized";
        return response;
    }

    try {
        uint64_t timestamp = getCurrentTimestamp();
        std::string payload = buildPayload(hardware_id, license_key, "validate", timestamp);

        std::string response_body = performHTTPRequest(backend_url, payload);
        response = parseResponse(response_body);

    } catch (const std::exception& e) {
        response.success = false;
        response.status = "error";
        response.error_code = "validation_failed";
        response.error_message = std::string("Validation error: ") + e.what();
    }

    return response;
}

ActivationClient::ActivationResponse ActivationClient::deactivateLicense(
    const std::string& hardware_id,
    const std::string& license_key,
    const std::string& backend_url
) {
    ActivationResponse response;

    if (!isValid()) {
        response.success = false;
        response.status = "error";
        response.error_code = "hmac_key_not_initialized";
        response.error_message = "HMAC key manager not initialized";
        return response;
    }

    try {
        uint64_t timestamp = getCurrentTimestamp();
        std::string payload = buildPayload(hardware_id, license_key, "deactivate", timestamp);

        std::string response_body = performHTTPRequest(backend_url, payload);
        response = parseResponse(response_body);

    } catch (const std::exception& e) {
        response.success = false;
        response.status = "error";
        response.error_code = "deactivation_failed";
        response.error_message = std::string("Deactivation error: ") + e.what();
    }

    return response;
}

std::string ActivationClient::performHTTPRequest(
    const std::string& url,
    const std::string& payload
) const {
    // Sign the payload
    std::string signature = hmac_manager_->computeHMAC(payload);

    // Use libcurl for HTTP requests
    CURL* curl = curl_easy_init();
    if (!curl) {
        throw std::runtime_error("Failed to initialize CURL");
    }

    std::string response_body;

    try {
        // Set URL
        curl_easy_setopt(curl, CURLOPT_URL, url.c_str());

        // Set timeout
        curl_easy_setopt(curl, CURLOPT_TIMEOUT_MS, static_cast<long>(network_timeout_ms_));

        // Set HTTP POST with JSON payload
        curl_easy_setopt(curl, CURLOPT_POST, 1L);
        curl_easy_setopt(curl, CURLOPT_POSTFIELDS, payload.c_str());

        // Set headers
        struct curl_slist* headers = nullptr;
        headers = curl_slist_append(headers, "Content-Type: application/json");

        std::string sig_header = "X-Graylight-Signature: " + signature;
        headers = curl_slist_append(headers, sig_header.c_str());

        curl_easy_setopt(curl, CURLOPT_HTTPHEADER, headers);

        // Write response callback (simplified - in production use proper callback)
        curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION,
            [](void* contents, size_t size, size_t nmemb, std::string* userp) -> size_t {
                userp->append((char*)contents, size * nmemb);
                return size * nmemb;
            });
        curl_easy_setopt(curl, CURLOPT_WRITEDATA, &response_body);

        // Perform request
        CURLcode res = curl_easy_perform(curl);
        if (res != CURLE_OK) {
            std::string error = curl_easy_strerror(res);
            throw std::runtime_error("HTTP request failed: " + error);
        }

        // Check HTTP response code
        long http_code = 0;
        curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &http_code);

        if (http_code < 200 || http_code >= 400) {
            throw std::runtime_error("HTTP error: " + std::to_string(http_code));
        }

        curl_slist_free_all(headers);

    } catch (...) {
        curl_easy_cleanup(curl);
        throw;
    }

    curl_easy_cleanup(curl);
    return response_body;
}

ActivationClient::ActivationResponse ActivationClient::parseResponse(
    const std::string& response_body
) const {
    ActivationResponse response;

    try {
        json response_json = json::parse(response_body);

        response.raw_response = response_json;

        // Check for error response
        if (response_json.contains("error")) {
            response.success = false;
            response.error_code = response_json.value("error", "unknown_error");
            response.error_message = response_json.value("message", "Unknown error");
            response.status = "error";
            return response;
        }

        // Parse success response
        response.success = response_json.value("success", false);
        response.status = response_json.value("status", "unknown");
        response.license_key = response_json.value("license_key", "");
        response.email = response_json.value("email", "");
        response.expiration_date = response_json.value("expiration_date", "");
        response.machine_limit = response_json.value("machine_limit", 0);

        // Count activated machines
        if (response_json.contains("machines_activated")) {
            auto machines = response_json["machines_activated"];
            if (machines.is_array()) {
                response.machines_activated = machines.size();
            }
        }

    } catch (const json::exception& e) {
        response.success = false;
        response.status = "error";
        response.error_code = "parse_error";
        response.error_message = std::string("Failed to parse response: ") + e.what();
    }

    return response;
}

} // namespace Licensing
