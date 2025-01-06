<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\MultiTenancy\Messenger;

use Parthenon\MultiTenancy\Creator\TenantCreatorInterface;
use Parthenon\MultiTenancy\Entity\Tenant;
use PHPUnit\Framework\TestCase;

class TenantCreatorHandlerTest extends TestCase
{
    public function testCreatesTenant()
    {
        $tenant = new Tenant();

        $tenantCreator = $this->createMock(TenantCreatorInterface::class);
        $tenantCreator->expects($this->once())->method('createTenant')->with($tenant);

        $subject = new TenantCreatorHandler($tenantCreator);
        $subject->__invoke($tenant);
    }
}
