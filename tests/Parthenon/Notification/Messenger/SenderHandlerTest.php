<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
