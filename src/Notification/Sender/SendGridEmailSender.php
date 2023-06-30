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

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Configuration;
use Parthenon\Notification\EmailInterface;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\Notification\Exception\UnableToSendMessageException;
use SendGrid\Mail\Attachment;
use SendGrid\Mail\Mail;

final class SendGridEmailSender implements EmailSenderInterface
{
    use LoggerAwareTrait;

    private \SendGrid $sendGrid;

    private Configuration $configuration;

    public function __construct(\SendGrid $sendGrid, Configuration $configuration)
    {
        $this->sendGrid = $sendGrid;
        $this->configuration = $configuration;
    }

    public function send(EmailInterface $message)
    {
        $email = new Mail();
        $email->setFrom($message->getFromAddress() ?? $this->configuration->getFromAddress(), $message->getFromName() ?? $this->configuration->getFromName());
        $email->addTo($message->getToAddress(), $message->getToName());

        if ($message->isTemplate()) {
            $email->setTemplateId($message->getTemplateName());
            $email->addDynamicTemplateDatas($message->getTemplateVariables());
        } else {
            $email->setSubject($message->getSubject());
            $email->addContent('text/html', $message->getContent());
        }

        foreach ($message->getAttachments() as $attachment) {
            $sendgridAttachment = new Attachment();
            $sendgridAttachment->setContent($attachment->getContent());
            $sendgridAttachment->setFilename($attachment->getName());
            $sendgridAttachment->setDisposition('attachment');
            $email->addAttachment($sendgridAttachment);
        }

        try {
            $response = $this->sendGrid->send($email);
            if (202 !== $response->statusCode()) {
                throw new \Exception($response->body());
            }
            $this->getLogger()->info('Sent email via sendgrid', ['to_address' => $message->getToAddress(), 'body' => $response->body(), 'status_code' => $response->statusCode()]);
        } catch (\Throwable $e) {
            $this->getLogger()->warning('Unable to send email via sendgrid', ['exception' => $e]);
            throw new UnableToSendMessageException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
