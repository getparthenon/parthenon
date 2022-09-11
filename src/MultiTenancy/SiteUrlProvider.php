<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
