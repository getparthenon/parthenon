<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
