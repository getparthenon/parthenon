<?php

namespace Parthenon\Payments\PaymentProvider\TransactionCloud;

final class Config
{
    public const DEFAULT_CUSTOMER_ID_PARAMETER = 'customerId';
    public const DEFAULT_PAYMENT_ID_PARAMETER = 'paymentId';

    public function __construct(
        private string $apiKey,
        private string $apiKeyPassword,
        private bool $sandbox,
        private string $customerIdParameter,
        private string $paymentIdParameter,
    ) {
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getApiKeyPassword(): string
    {
        return $this->apiKeyPassword;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * @return string
     */
    public function getCustomerIdParameter(): string
    {
        return $this->customerIdParameter;
    }

    /**
     * @return string
     */
    public function getPaymentIdParameter(): string
    {
        return $this->paymentIdParameter;
    }
}
