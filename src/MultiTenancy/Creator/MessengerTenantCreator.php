<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\MultiTenancy\Creator;

use Parthenon\MultiTenancy\Entity\TenantInterface;
use Parthenon\MultiTenancy\Exception\TenantCreationFailureException;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerTenantCreator implements TenantCreatorInterface
{
    public function __construct(private MessageBusInterface $messengerBus)
    {
    }

    public function createTenant(TenantInterface $tenant): void
    {
        try {
            $this->messengerBus->dispatch($tenant);
        } catch (\Exception $e) {
            throw new TenantCreationFailureException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
