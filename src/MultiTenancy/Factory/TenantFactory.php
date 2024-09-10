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

namespace Parthenon\MultiTenancy\Factory;

use Parthenon\MultiTenancy\Entity\TenantInterface;
use Parthenon\MultiTenancy\Model\SignUp;

final class TenantFactory implements TenantFactoryInterface
{
    public function __construct(
        private TenantInterface $tenant,
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
