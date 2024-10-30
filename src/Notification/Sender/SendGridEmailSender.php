<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
