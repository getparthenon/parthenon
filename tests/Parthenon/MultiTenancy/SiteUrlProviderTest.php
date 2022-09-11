<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy;

use Parthenon\MultiTenancy\Entity\Tenant;
use Parthenon\MultiTenancy\Exception\NoTenantFoundException;
use Parthenon\MultiTenancy\TenantProvider\TenantProviderInterface;
use PHPUnit\Framework\TestCase;

class SiteUrlProviderTest extends TestCase
{
    public function testReturnsSiteUrl()
    {
        $currentTenantProvider = $this->createMock(TenantProviderInterface::class);

        $tenant = new Tenant();
        $tenant->setSubdomain('happy');

        $currentTenantProvider->method('getCurrentTenant')->willReturn($tenant);

        $siteUrlProvider = new SiteUrlProvider('parthenon.cloud', 'getparthenon.com', $currentTenantProvider);
        $this->assertEquals('https://happy.parthenon.cloud', $siteUrlProvider->getSiteUrl());
    }

    public function testReturnGlobalIfNoTenant()
    {
        $currentTenantProvider = $this->createMock(TenantProviderInterface::class);

        $tenant = new Tenant();
        $tenant->setSubdomain('happy');

        $currentTenantProvider->method('getCurrentTenant')->willThrowException(new NoTenantFoundException());

        $siteUrlProvider = new SiteUrlProvider('parthenon.cloud', 'getparthenon.com', $currentTenantProvider);
        $this->assertEquals('getparthenon.com', $siteUrlProvider->getSiteUrl());
    }
}
