<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification\Messenger;

use Parthenon\Notification\Email;
use Parthenon\Notification\EmailSenderInterface;
use PHPUnit\Framework\TestCase;

class SenderHandlerTest extends TestCase
{
    public function testCallSenders()
    {
        $email = new Email();
        $sender = $this->createMock(EmailSenderInterface::class);
        $sender->expects($this->once())->method('send')->with($email);

        $handler = new SenderHandler($sender);
        $handler($email);
    }
}
