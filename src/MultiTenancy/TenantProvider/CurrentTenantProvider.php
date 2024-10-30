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

namespace Parthenon\MultiTenancy\TenantProvider;

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
    private bool $previouslyFailed = false;

    public function __construct(
        private TenantRepositoryInterface $tenantRepository,
        private RequestStack $requestStack,
        private string $defaultDatabase,
        private string $domain,
    ) {
    }

    public function setTenant(TenantInterface $tenant): void
    {
        $this->tenant = $tenant;
    }

    /**
     * @throws NoTenantFoundException
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
        if (!str_ends_with($host, $this->domain)) {
            return Tenant::createWithSubdomainAndDatabase($this->defaultDatabase, 'dummy.subdomain');
        }
        $subdomain = preg_replace('~^([a-z0-9-]+)(\..*)$~', '$1', $host);

        if ($this->previouslyFailed && !$refresh) {
            // If it's already failed other attempts by other possible factors will fail too.
            throw new NoTenantFoundException(sprintf('Previously Unable to find tenant for \'%s\'', $subdomain));
        }

        try {
            $this->tenant = $this->tenantRepository->findBySubdomain($subdomain);
        } catch (NoEntityFoundException $e) {
            $this->previouslyFailed = true;
            throw new NoTenantFoundException(sprintf('Unable to find tenant for \'%s\'', $subdomain), $e->getCode(), $e);
        }

        return $this->tenant;
    }
}
