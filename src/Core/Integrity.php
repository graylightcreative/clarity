<?php

namespace Clarity\Core;

/**
 * SOVEREIGN INTEGRITY HANDSHAKE
 * Validates the X-GL-* integrity headers for all API traffic.
 */
class Integrity
{
    private const TIMESTAMP_WINDOW = 300; // 5 minute replay window

    /**
     * @param string $apiKey The tenant-specific API key.
     * @param string $secret The secret key used for HMAC-SHA256.
     * @return bool
     */
    public static function validateHandshake(string $apiKey, string $secret): bool
    {
        $headers = getallheaders();

        $clientApiKey = $headers['X-GL-API-KEY'] ?? null;
        $timestamp = (int)($headers['X-GL-TIMESTAMP'] ?? 0);
        $signature = $headers['X-GL-SIGNATURE'] ?? null;

        if (!$clientApiKey || !$timestamp || !$signature) {
            return false;
        }

        // 1. Verify API Key
        if ($clientApiKey !== $apiKey) {
            return false;
        }

        // 2. Verify Replay Window
        if (abs(time() - $timestamp) > self::TIMESTAMP_WINDOW) {
            return false;
        }

        // 3. Verify Signature (HMAC-SHA256)
        // Payload for signature is: API_KEY . TIMESTAMP . HTTP_METHOD . REQUEST_URI
        $payload = $clientApiKey . $timestamp . $_SERVER['REQUEST_METHOD'] . $_SERVER['REQUEST_URI'];
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Validates BEACON fleet_token cookie for administrative/user access.
     */
    public static function validateBeaconSession(): bool
    {
        $fleetToken = $_COOKIE['fleet_token'] ?? null;
        if (!$fleetToken) {
            return false;
        }

        // In a real Rig environment, we would handshake with auth.starrship1.com
        // For now, we stub the validation logic for the blueprint.
        return true;
    }
}
