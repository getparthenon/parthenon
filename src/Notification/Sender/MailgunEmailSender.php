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

use Mailgun\Mailgun;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Configuration;
use Parthenon\Notification\EmailInterface;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\Notification\Exception\UnableToSendMessageException;

final class MailgunEmailSender implements EmailSenderInterface
{
    use LoggerAwareTrait;

    public function __construct(private Mailgun $mailgun, private string $domain, private Configuration $configuration)
    {
    }

    public function send(EmailInterface $message)
    {
        $messageArray = [
            'to' => $message->getToAddress(),
            'from' => $message->getFromAddress(),
            'subject' => $message->getSubject(),
        ];

        if ($message->isTemplate()) {
            $messageArray['template'] = $message->getTemplateName();
            $messageArray['h:X-Mailgun-Variables'] = json_encode($message->getTemplateVariables());
        } else {
            $messageArray['html'] = $message->getContent();
        }

        $attachments = $message->getAttachments();
        if (!empty($attachments)) {
            $messageArray['attachment'] = [];
            foreach ($attachments as $attachment) {
                $messageArray['attachment'][] = ['fileContent' => $attachment->getContent(), 'filename' => $attachment->getName()];
            }
        }

        try {
            $response = $this->mailgun->messages()->send($this->domain, $messageArray);
            $this->getLogger()->info('Sent email via mail gun');
        } catch (\Exception $e) {
            $this->getLogger()->warning('Unable to send email via mailgun', ['exception_message' => $e->getMessage()]);
            throw new UnableToSendMessageException($e->getMessage(), $e->getCode(), $e);
        }

        if (200 !== $response->getStatusCode()) {
            $this->getLogger()->warning('Unable to send email via mailgun', ['status_code' => $response->getStatusCode()]);
            throw new UnableToSendMessageException(sprintf('Mailgun returned %d status code', $response->getStatusCode()));
        }
    }
}
