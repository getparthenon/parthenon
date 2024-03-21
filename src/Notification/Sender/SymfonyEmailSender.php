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
