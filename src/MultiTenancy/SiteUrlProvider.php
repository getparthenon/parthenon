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

namespace Parthenon\MultiTenancy;

use Parthenon\Common\Config\SiteUrlProviderInterface;
use Parthenon\MultiTenancy\Exception\NoTenantFoundException;
use Parthenon\MultiTenancy\TenantProvider\TenantProviderAwareInterface;
use Parthenon\MultiTenancy\TenantProvider\TenantProviderInterface;

class SiteUrlProvider implements SiteUrlProviderInterface, TenantProviderAwareInterface
{
    public function __construct(
        private string $domain,
        private string $siteUrl,
        private TenantProviderInterface $tenantProvider
    ) {
    }

    public function getSiteUrl(): string
    {
        try {
            $tenant = $this->tenantProvider->getCurrentTenant();

            return sprintf('https://%s.%s', $tenant->getSubdomain(), $this->domain);
        } catch (NoTenantFoundException $exception) {
            return $this->siteUrl;
        }
    }

    public function setTenantProvider(TenantProviderInterface $tenantProvider): void
    {
        $this->tenantProvider = $tenantProvider;
    }
}
