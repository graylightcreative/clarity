<?php

namespace Clarity\Finance;

use Clarity\Core\Integrity;

/**
 * CHANCELLOR // FINANCIAL ORCHESTRATOR
 * Bridges Clarity NGN with the Graylight Stripe Clearinghouse.
 */
class Chancellor
{
    private string $apiKey;
    private string $secretKey;
    private const CHANCELLOR_URL = 'https://auth.starrship1.com/v1/chancellor/authorize-checkout';

    public function __construct(string $apiKey, string $secretKey)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
    }

    /**
     * Authorizes a Founder's Acquisition ($199) through the Chancellor.
     * Enforces the NGN_90_10 split.
     */
    public function authorizeAcquisition(): array
    {
        $timestamp = time();
        $payload = [
            "user_id" => Integrity::getUserId(),
            "type" => "ONE_TIME_ACQUISITION",
            "line_items" => [
                [
                    "price_data" => [
                        "currency" => "usd",
                        "product_data" => [
                            "name" => "Clarity NGN - Founder's Acquisition",
                            "description" => "Foundational access to the NextGen Noise Clarity Node."
                        ],
                        "unit_amount" => 19900
                    ],
                    "quantity" => 1
                ]
            ],
            "metadata" => [
                "success_url" => "https://clarity.nextgennoise.com/activation/success",
                "cancel_url" => "https://clarity.nextgennoise.com/acquisition/cancel",
                "split_logic" => "NGN_90_10",
                "ledger_allocation" => "PRODUCT:NGN_REVENUE_90|INFRA:SOVEREIGN_INFRASTRUCTURE_FEE_10"
            ]
        ];

        $rawPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = Integrity::generateFleetSignature($rawPayload, $timestamp, $this->secretKey);

        $headers = [
            'Content-Type: application/json',
            'X-GL-API-KEY: ' . $this->apiKey,
            'X-GL-TIMESTAMP: ' . $timestamp,
            'X-GL-SIGNATURE: ' . $signature
        ];

        return $this->sendToChancellor($rawPayload, $headers);
    }

    /**
     * Raw CURL request to the Chancellor clearinghouse.
     */
    private function sendToChancellor(string $rawPayload, array $headers): array
    {
        $ch = curl_init(self::CHANCELLOR_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200) {
            return [
                "success" => false,
                "error" => "CHANCELLOR_REJECTION // HTTP_CODE: " . $httpCode . " // CURL_ERR: " . $curlError,
                "raw" => $response
            ];
        }

        return json_decode($response, true);
    }
}
