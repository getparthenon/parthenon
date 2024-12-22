<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

namespace Parthenon\MultiTenancy;

use Parthenon\Common\Config\SiteUrlProviderInterface;
use Parthenon\MultiTenancy\Exception\NoTenantFoundException;
use Parthenon\MultiTenancy\TenantProvider\TenantProviderAwareInterface;
use Parthenon\MultiTenancy\TenantProvider\TenantProviderInterface;

class SiteUrlProvider implements SiteUrlProviderInterface, TenantProviderAwareInterface
{
    public function __construct(
        private string $format,
        private string $siteUrl,
        private TenantProviderInterface $tenantProvider,
    ) {
    }

    public function getSiteUrl(): string
    {
        try {
            $tenant = $this->tenantProvider->getCurrentTenant();

            return sprintf($this->format, $tenant->getSubdomain());
        } catch (NoTenantFoundException $exception) {
            return $this->siteUrl;
        }
    }

    public function setTenantProvider(TenantProviderInterface $tenantProvider): void
    {
        $this->tenantProvider = $tenantProvider;
    }
}
