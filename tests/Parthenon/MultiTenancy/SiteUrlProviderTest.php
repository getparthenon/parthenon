<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
