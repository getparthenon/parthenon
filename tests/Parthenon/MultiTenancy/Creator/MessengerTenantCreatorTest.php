<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Creator;

use Parthenon\MultiTenancy\Entity\Tenant;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerTenantCreatorTest extends TestCase
{
    public function testMessenger()
    {
        $tenant = new Tenant();

        $messengerBus = $this->createMock(MessageBusInterface::class);
        $messengerBus->expects($this->once())->method('dispatch')->with($tenant);

        $subject = new MessengerTenantCreator($messengerBus);
        $subject->createTenant($tenant);
    }
}
