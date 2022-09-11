<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification;

use Parthenon\Notification\Exception\UnableToSendMessageException;

interface EmailSenderInterface
{
    /**
     * @throws UnableToSendMessageException
     */
    public function send(EmailInterface $message);
}
