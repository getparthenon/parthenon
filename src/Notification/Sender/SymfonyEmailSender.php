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

namespace Parthenon\Notification\Sender;

use Parthenon\Notification\Configuration;
use Parthenon\Notification\EmailInterface;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\Notification\Exception\UnableToSendMessageException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class SymfonyEmailSender implements EmailSenderInterface
{
    public function __construct(private MailerInterface $mailer, private Configuration $configuration)
    {
    }

    public function send(EmailInterface $message)
    {
        try {
            if ($message->isTemplate()) {
                $email = (new TemplatedEmail())
                    ->from($this->configuration->getFromAddress())
                    ->to($message->getToAddress())
                    ->subject($message->getSubject())
                    ->htmlTemplate($message->getTemplateName())
                    ->context($message->getTemplateVariables());
            } else {
                $email = (new Email())
                    ->from($this->configuration->getFromAddress())
                    ->to($message->getToAddress())
                    ->subject($message->getSubject())
                    ->text($message->getContent());
            }

            foreach ($message->getAttachments() as $attachment) {
                $email->attach($attachment->getContent(), $attachment->getName());
            }

            $this->mailer->send($email);
        } catch (\Exception $e) {
            throw new UnableToSendMessageException('Unable to send email', previous: $e);
        }
    }
}
