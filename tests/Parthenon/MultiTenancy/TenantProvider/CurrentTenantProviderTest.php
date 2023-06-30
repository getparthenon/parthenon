<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\MultiTenancy\TenantProvider;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\MultiTenancy\Entity\Tenant;
use Parthenon\MultiTenancy\Exception\NoTenantFoundException;
use Parthenon\MultiTenancy\Repository\TenantRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CurrentTenantProviderTest extends TestCase
{
    public function testReturnsTenant()
    {
        $tenantRepostioryInterface = $this->createMock(TenantRepositoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $request = $this->createMock(Request::class);
        $tenant = new Tenant();

        $requestStack->method('getMainRequest')->willReturn($request);
        $request->method('getHost')->willReturn('test.example.org');
        $tenantRepostioryInterface->method('findBySubdomain')->with('test')->willReturn($tenant);

        $currentTenantProvider = new CurrentTenantProvider($tenantRepostioryInterface, $requestStack, 'defaultDatabase');
        $this->assertSame($tenant, $currentTenantProvider->getCurrentTenant());
    }

    public function testThrowsExceptionWithNoException()
    {
        $this->expectException(NoTenantFoundException::class);
        $tenantRepostioryInterface = $this->createMock(TenantRepositoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $request = $this->createMock(Request::class);
        $tenant = new Tenant();

        $requestStack->method('getMainRequest')->willReturn($request);
        $request->method('getHost')->willReturn('test.example.org');
        $tenantRepostioryInterface->method('findBySubdomain')->with('test')->willThrowException(new NoEntityFoundException());

        $currentTenantProvider = new CurrentTenantProvider($tenantRepostioryInterface, $requestStack, 'defaultDatabase');
        $currentTenantProvider->getCurrentTenant();
    }
}
