<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Notification\Sender;

use Parthenon\Notification\Email;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBus;

class MessengerEmailTest extends TestCase
{
    public function testSendsToMessageBus()
    {
        $messageBus = $this->createMock(MessageBus::class);
        $email = new Email();

        $messageBus->expects($this->once())->method('dispatch')->with($email);

        $messagerSender = new MessengerEmailSender($messageBus);
        $messagerSender->send($email);
    }
}
