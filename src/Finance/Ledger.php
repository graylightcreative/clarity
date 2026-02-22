<?php

namespace Clarity\Finance;

/**
 * LEDGER INTEGRATION (THE 90/10 SPLIT MANDATE)
 * Every dollar earned is split: 90% Product, 10% Infrastructure.
 */
class Ledger
{
    private string $ledgerApiKey;
    private const LEDGER_API_URL = 'https://ledger.graylightcreative.com/api/v1/transactions/record-payment';
    private const SPLIT_PRODUCT = 0.90;
    private const SPLIT_INFRA   = 0.10;

    public function __construct(string $ledgerApiKey)
    {
        $this->ledgerApiKey = $ledgerApiKey;
    }

    /**
     * @param string $transactionId The unique Stripe/payment transaction ID.
     * @param float $grossAmount The total amount paid by the user.
     * @param string $userEmail The email associated with the purchase.
     * @return bool
     */
    public function recordTransaction(string $transactionId, float $grossAmount, string $userEmail): bool
    {
        $productAmount = round($grossAmount * self::SPLIT_PRODUCT, 2);
        $infraAmount   = round($grossAmount * self::SPLIT_INFRA, 2);

        $payload = [
            'transaction_id' => $transactionId,
            'source' => 'stripe',
            'user_email' => $userEmail,
            'gross_amount' => $grossAmount,
            'currency' => 'USD',
            'description' => 'CLARITY NGN // SOVEREIGN PURCHASE',
            'split_percentage' => '90/10',
            'entries' => [
                [
                    'account' => 'NGN_REVENUE_90',
                    'amount' => $productAmount,
                    'type' => 'CREDIT'
                ],
                [
                    'account' => 'SOVEREIGN_INFRASTRUCTURE_FEE_10',
                    'amount' => $infraAmount,
                    'type' => 'CREDIT'
                ]
            ]
        ];

        return $this->sendToLedger($payload);
    }

    /**
     * Performs a raw HTTP POST to the Sovereign Ledger.
     */
    private function sendToLedger(array $payload): bool
    {
        $ch = curl_init(self::LEDGER_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->ledgerApiKey,
            'X-Service: clarity-ngn',
            'X-Request-ID: ' . uniqid('ledger_', true)
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode === 201 || $httpCode === 200);
    }
}
