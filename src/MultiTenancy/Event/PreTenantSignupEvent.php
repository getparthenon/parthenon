<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Event;

use Parthenon\MultiTenancy\Entity\TenantInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class PreTenantSignupEvent extends Event
{
    public const NAME = 'parthenon.multi_tenancy.signup.pre';

    public function __construct(private TenantInterface $tenant)
    {
    }

    public function getTenant(): TenantInterface
    {
        return $this->tenant;
    }
}
