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

namespace Parthenon\Payments\Stripe;

use Parthenon\Payments\ConfigInterface;

class Config implements ConfigInterface
{
    public const PROVIDER_NAME = 'stripe';

    public function __construct(private string $publicApiKey, private string $privateApiKey, private string $successUrl, private string $cancelUrl, private string $returnUrl)
    {
    }

    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    public function getCancelUrl(): string
    {
        return $this->cancelUrl;
    }

    public function getPublicApiKey(): string
    {
        return $this->publicApiKey;
    }

    public function getPrivateApiKey(): string
    {
        return $this->privateApiKey;
    }

    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }

    public function getConfigPublicPayload(): array
    {
        return [
            'provider' => self::PROVIDER_NAME,
            'api_key' => $this->publicApiKey,
        ];
    }
}
