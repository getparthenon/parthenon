<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
