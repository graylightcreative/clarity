<?php

namespace Clarity\Core;

/**
 * SOVEREIGN INTEGRITY HANDSHAKE
 * Validates and generates X-GL-* integrity headers for fleet-wide traffic.
 */
class Integrity
{
    private const TIMESTAMP_WINDOW = 300; // 5 minute replay window

    /**
     * Validates incoming handshake from other fleet nodes.
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

        if ($clientApiKey !== $apiKey) {
            return false;
        }

        if (abs(time() - $timestamp) > self::TIMESTAMP_WINDOW) {
            return false;
        }

        $payload = $clientApiKey . $timestamp . $_SERVER['REQUEST_METHOD'] . $_SERVER['REQUEST_URI'];
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generates an HMAC-SHA256 signature for outgoing Chancellor/Fleet requests.
     */
    public static function generateFleetSignature(string $rawPayload, int $timestamp, string $secret): string
    {
        return hash_hmac('sha256', $rawPayload . $timestamp, $secret);
    }

    /**
     * Validates BEACON fleet_token and handles cross-domain token capture.
     */
    public static function validateBeaconSession(): bool
    {
        // 1. Capture token from URL (returned from Beacon redirect)
        if (isset($_GET['fleet_token'])) {
            $token = preg_replace('/[^a-zA-Z0-9\._\-]/', '', $_GET['fleet_token']);
            setcookie('fleet_token', $token, [
                'expires' => time() + 86400 * 30, // 30 day persistence
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            $_COOKIE['fleet_token'] = $token; // Pressurize immediate session
            return true;
        }

        // 2. Validate existing cookie
        return isset($_COOKIE['fleet_token']);
    }

    /**
     * Returns the current user ID from the Beacon session.
     * In a production Rig, this decodes the fleet_token JWT.
     */
    public static function getUserId(): int
    {
        // Stubbed for blueprint; in production, this extracts 'sub' from the JWT.
        return 1; 
    }
}
