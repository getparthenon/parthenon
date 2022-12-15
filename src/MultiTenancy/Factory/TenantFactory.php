<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
