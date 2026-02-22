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
     * Protocol: hash_hmac('sha256', raw_payload + timestamp, CLARITY_SECRET_KEY)
     */
    public static function generateFleetSignature(string $rawPayload, int $timestamp, string $secret): string
    {
        return hash_hmac('sha256', $rawPayload . $timestamp, $secret);
    }

    /**
     * Validates BEACON fleet_token cookie for administrative/user access.
     */
    public static function validateBeaconSession(): bool
    {
        return isset($_COOKIE['fleet_token']);
    }

    /**
     * Returns the current user ID from the Beacon session.
     * Stubbed for blueprint; in production, this decodes the fleet_token JWT.
     */
    public static function getUserId(): int
    {
        return 1; 
    }
}
