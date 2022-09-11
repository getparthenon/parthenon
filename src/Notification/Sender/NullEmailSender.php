<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification\Sender;

use Parthenon\Notification\EmailInterface;
use Parthenon\Notification\EmailSenderInterface;

final class NullEmailSender implements EmailSenderInterface
{
    /**
     * {@inheritdoc}
     */
    public function send(EmailInterface $message)
    {
    }
}
