<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
