<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
