<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Notification\Sender;

use Parthenon\Notification\Attachment;
use Parthenon\Notification\Configuration;
use Parthenon\Notification\EmailInterface;
use Parthenon\Notification\Exception\UnableToSendMessageException;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SymfonyEmailSenderTest extends TestCase
{
    public const TO_EMAIL = 'to@example.org';
    public const FROM_EMAIL = 'from@example.org';
    public const SUBJECT = 'Subject';
    public const CONTENT = 'An email from a test';
    public const TEMPLATE_NAME = 'template_name';
    public const TEMPLATE_ARRAY = ['values' => 'value'];

    public function testThrowsParthenonException()
    {
        $this->expectException(UnableToSendMessageException::class);

        $mailer = $this->createMock(MailerInterface::class);
        $message = $this->createMock(EmailInterface::class);
        $configuration = $this->createMock(Configuration::class);

        $message->method('isTemplate')->willReturn(false);
        $configuration->method('getFromAddress')->willReturn(self::FROM_EMAIL);
        $configuration->method('getFromName')->willReturn(self::FROM_EMAIL);
        $message->method('getToAddress')->willReturn(self::TO_EMAIL);
        $message->method('getToName')->willReturn(self::TO_EMAIL);
        $message->method('getSubject')->willReturn(self::SUBJECT);
        $message->method('getContent')->willReturn(self::CONTENT);
        $message->method('getAttachments')->willReturn([]);

        $mailer->expects($this->once())->method('send')->with($this->isInstanceOf(Email::class))->willThrowException(new \Exception());

        $sender = new SymfonyEmailSender($mailer, $configuration);
        $sender->send($message);
    }

    public function testSendsNonTemplate()
    {
        $mailer = $this->createMock(MailerInterface::class);
        $message = $this->createMock(EmailInterface::class);
        $configuration = $this->createMock(Configuration::class);

        $message->method('isTemplate')->willReturn(false);
        $configuration->method('getFromAddress')->willReturn(self::FROM_EMAIL);
        $configuration->method('getFromName')->willReturn(self::FROM_EMAIL);
        $message->method('getToAddress')->willReturn(self::TO_EMAIL);
        $message->method('getToName')->willReturn(self::TO_EMAIL);
        $message->method('getSubject')->willReturn(self::SUBJECT);
        $message->method('getContent')->willReturn(self::CONTENT);
        $message->method('getAttachments')->willReturn([]);

        $mailer->expects($this->once())->method('send')->with($this->isInstanceOf(Email::class));

        $sender = new SymfonyEmailSender($mailer, $configuration);
        $sender->send($message);
    }

    public function testSendsTemplate()
    {
        $mailer = $this->createMock(MailerInterface::class);
        $message = $this->createMock(EmailInterface::class);
        $configuration = $this->createMock(Configuration::class);

        $message->method('isTemplate')->willReturn(true);
        $configuration->method('getFromAddress')->willReturn(self::FROM_EMAIL);
        $configuration->method('getFromName')->willReturn(self::FROM_EMAIL);
        $message->method('getToAddress')->willReturn(self::TO_EMAIL);
        $message->method('getToName')->willReturn(self::TO_EMAIL);
        $message->method('getSubject')->willReturn(self::SUBJECT);
        $message->method('getTemplateName')->willReturn(self::TEMPLATE_NAME);
        $message->method('getTemplateVariables')->willReturn(self::TEMPLATE_ARRAY);
        $message->method('getAttachments')->willReturn([]);

        $mailer->expects($this->once())->method('send')->with($this->isInstanceOf(Email::class));

        $sender = new SymfonyEmailSender($mailer, $configuration);
        $sender->send($message);
    }

    public function testSendsTemplateWithAttachment()
    {
        $attachment = $this->createMock(Attachment::class);
        $mailer = $this->createMock(MailerInterface::class);
        $message = $this->createMock(EmailInterface::class);
        $configuration = $this->createMock(Configuration::class);

        $attachment->method('getContent')->willReturn('content here');
        $attachment->method('getName')->willReturn('text_file.txt');

        $message->method('isTemplate')->willReturn(true);
        $configuration->method('getFromAddress')->willReturn(self::FROM_EMAIL);
        $configuration->method('getFromName')->willReturn(self::FROM_EMAIL);
        $message->method('getToAddress')->willReturn(self::TO_EMAIL);
        $message->method('getToName')->willReturn(self::TO_EMAIL);
        $message->method('getSubject')->willReturn(self::SUBJECT);
        $message->method('getTemplateName')->willReturn(self::TEMPLATE_NAME);
        $message->method('getTemplateVariables')->willReturn(self::TEMPLATE_ARRAY);
        $message->method('getAttachments')->willReturn([$attachment]);

        $mailer->expects($this->once())->method('send')->with($this->isInstanceOf(TemplatedEmail::class));

        $sender = new SymfonyEmailSender($mailer, $configuration);
        $sender->send($message);
    }
}
