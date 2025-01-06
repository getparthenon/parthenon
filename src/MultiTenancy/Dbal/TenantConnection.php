<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\MultiTenancy\Dbal;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Parthenon\MultiTenancy\Entity\TenantInterface;
use Parthenon\MultiTenancy\TenantProvider\TenantProviderInterface;

class TenantConnection extends Connection
{
    private TenantProviderInterface $currentTenantProvider;
    private TenantInterface $tenant;
    private bool $connected = false;

    private array $params;

    public function __construct(array $params, Driver $driver, ?Configuration $config = null, ?EventManager $eventManager = null)
    {
        $this->params = $params;
        parent::__construct($params, $driver, $config, $eventManager);
    }

    public function setCurrentTenantProvider(TenantProviderInterface $currentTenantProvider): void
    {
        $this->currentTenantProvider = $currentTenantProvider;
    }

    public function connect(bool $refresh = false): bool
    {
        if ($this->connected && !$refresh) {
            return false;
        }

        $tenant = $this->currentTenantProvider->getCurrentTenant();

        $this->close();
        $this->tenant = $tenant;

        $this->params['dbname'] = $tenant->getDatabase();
        $this->_conn = $this->_driver->connect($this->params);
        $this->connected = true;
        if ($this->_eventManager->hasListeners(Events::postConnect)) {
            $eventArgs = new ConnectionEventArgs($this);
            $this->_eventManager->dispatchEvent(Events::postConnect, $eventArgs);
        }

        return true;
    }
}
