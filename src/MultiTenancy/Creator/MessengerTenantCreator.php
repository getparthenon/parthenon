<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
