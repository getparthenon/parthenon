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
