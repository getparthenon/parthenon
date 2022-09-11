<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments\Stripe;

class Config
{
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
}
