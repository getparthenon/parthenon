<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\PaymentProvider\TransactionCloud;

use Parthenon\Payments\ConfigInterface;

final class Config implements ConfigInterface
{
    public const DEFAULT_CUSTOMER_ID_PARAMETER = 'customerId';
    public const DEFAULT_PAYMENT_ID_PARAMETER = 'paymentId';
    public const PROVIDER_NAME = 'transactioncloud';

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

    public function getCustomerIdParameter(): string
    {
        return $this->customerIdParameter;
    }

    public function getPaymentIdParameter(): string
    {
        return $this->paymentIdParameter;
    }

    public function getConfigPublicPayload(): array
    {
        return ['provider' => self::PROVIDER_NAME];
    }
}
