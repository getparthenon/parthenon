<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
