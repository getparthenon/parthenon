<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification\Messenger;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Email;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SenderHandler implements MessageHandlerInterface
{
    use LoggerAwareTrait;

    public function __construct(private EmailSenderInterface $sender)
    {
    }

    public function __invoke(Email $message)
    {
        $this->getLogger()->info('Sending email from Message');

        $this->sender->send($message);
    }
}
