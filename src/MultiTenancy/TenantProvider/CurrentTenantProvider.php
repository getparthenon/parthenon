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
