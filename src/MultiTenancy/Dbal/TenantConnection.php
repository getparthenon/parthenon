<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
