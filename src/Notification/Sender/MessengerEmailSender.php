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

namespace Parthenon\Notification\Sender;

use Parthenon\Notification\EmailInterface;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\Notification\Exception\UnableToSendMessageException;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerEmailSender implements EmailSenderInterface
{
    public function __construct(private MessageBusInterface $messengerBus)
    {
    }

    public function send(EmailInterface $message)
    {
        try {
            $this->messengerBus->dispatch($message);
        } catch (\Exception $e) {
            throw new UnableToSendMessageException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
