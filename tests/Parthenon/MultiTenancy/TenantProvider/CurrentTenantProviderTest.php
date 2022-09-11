<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
