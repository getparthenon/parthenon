<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments;

final class Checkout implements CheckoutInterface
{
    public function __construct(private string $id, private string $status = 'unknown', private ?string $subscriptionId = null)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }
}
