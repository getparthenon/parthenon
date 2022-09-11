<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
