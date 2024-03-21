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
