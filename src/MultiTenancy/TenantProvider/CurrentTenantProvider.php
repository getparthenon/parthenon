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

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\MultiTenancy\Entity\Tenant;
use Parthenon\MultiTenancy\Entity\TenantInterface;
use Parthenon\MultiTenancy\Exception\NoTenantFoundException;
use Parthenon\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CurrentTenantProvider implements TenantProviderInterface
{
    private TenantInterface $tenant;

    public function __construct(
        private TenantRepositoryInterface $tenantRepository,
        private RequestStack $requestStack,
        private string $defaultDatabase,
    ) {
    }

    public function setTenant(TenantInterface $tenant): void
    {
        $this->tenant = $tenant;
    }

    /**
     * @throws GeneralException
     */
    public function getCurrentTenant(bool $refresh = false): TenantInterface
    {
        if (isset($this->tenant) && !$refresh) {
            return $this->tenant;
        }

        $request = $this->requestStack->getMainRequest();

        if (!$request instanceof Request) {
            return Tenant::createWithSubdomainAndDatabase($this->defaultDatabase, 'dummy.subdomain');
        }

        $host = $request->getHost();
        $subdomain = preg_replace('~^([a-z0-9-]+)(\..*)$~', '$1', $host);

        try {
            $this->tenant = $this->tenantRepository->findBySubdomain($subdomain);
        } catch (NoEntityFoundException $e) {
            throw new NoTenantFoundException(sprintf('Unable to find tenant for \'%s\'', $subdomain), $e->getCode(), $e);
        }

        return $this->tenant;
    }
}
