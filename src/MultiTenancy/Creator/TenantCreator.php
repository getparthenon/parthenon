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

namespace Parthenon\MultiTenancy\Creator;

use Parthenon\MultiTenancy\Database\DatabaseCreatorInterface;
use Parthenon\MultiTenancy\Entity\TenantInterface;
use Parthenon\MultiTenancy\Event\TenantCreatedEvent;
use Parthenon\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TenantCreator implements TenantCreatorInterface
{
    public function __construct(
        private TenantRepositoryInterface $tenantRepository,
        private DatabaseCreatorInterface $databaseCreator,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function createTenant(TenantInterface $tenant): void
    {
        $tenant->setCreatedAt(new \DateTime());
        $this->tenantRepository->save($tenant);

        $this->databaseCreator->createDatabase($tenant);

        $this->dispatcher->dispatch(new TenantCreatedEvent($tenant), TenantCreatedEvent::NAME);
    }
}
