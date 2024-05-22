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

        $currentTenantProvider = new CurrentTenantProvider($tenantRepostioryInterface, $requestStack, 'defaultDatabase', 'example.org');
        $this->assertSame($tenant, $currentTenantProvider->getCurrentTenant());
    }

    public function testReturnsDummyTenant()
    {
        $tenantRepostioryInterface = $this->createMock(TenantRepositoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $request = $this->createMock(Request::class);
        $tenant = new Tenant();

        $requestStack->method('getMainRequest')->willReturn($request);
        $request->method('getHost')->willReturn('test.example.org');
        $tenantRepostioryInterface->expects($this->never())->method('findBySubdomain')->with('test')->willReturn($tenant);

        $currentTenantProvider = new CurrentTenantProvider($tenantRepostioryInterface, $requestStack, 'defaultDatabase', 'example.com');
        $this->assertNotSame($tenant, $currentTenantProvider->getCurrentTenant());
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

        $currentTenantProvider = new CurrentTenantProvider($tenantRepostioryInterface, $requestStack, 'defaultDatabase', 'example.org');
        $currentTenantProvider->getCurrentTenant();
    }
}
