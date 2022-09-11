<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Factory;

use Parthenon\MultiTenancy\Entity\TenantInterface;
use Parthenon\MultiTenancy\Model\SignUp;

final class TenantFactory implements TenantFactoryInterface
{
    public function __construct(
        private TenantInterface $tenant
    ) {
    }

    public function buildTenantFromSignUp(SignUp $signUp): TenantInterface
    {
        $className = get_class($this->tenant);
        /** @var TenantInterface $tenant */
        $tenant = new $className();
        $tenant->setDatabase(strtolower($signUp->getSubdomain()));
        $tenant->setSubdomain(strtolower($signUp->getSubdomain()));

        return $tenant;
    }
}
