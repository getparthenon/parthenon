<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
