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
