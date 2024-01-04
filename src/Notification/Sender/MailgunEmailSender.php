<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
