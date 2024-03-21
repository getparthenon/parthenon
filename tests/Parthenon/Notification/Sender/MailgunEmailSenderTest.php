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

use Mailgun\Api\Message;
use Mailgun\Mailgun;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\Configuration;
use Parthenon\Notification\EmailInterface;
use Parthenon\Notification\Exception\UnableToSendMessageException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class MailgunEmailSenderTest extends TestCase
{
    public const DOMAIN_COM = 'domain.com';

    public function testCallsMailgunNoTemplate()
    {
        $mailgun = $this->createMock(Mailgun::class);
        $message = $this->createMock(Message::class);
        $parthenonMessage = $this->createMock(EmailInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $configuration = $this->createMock(Configuration::class);

        $parthenonMessage->method('getFromAddress')->willReturn('sally@example.org');
        $parthenonMessage->method('getFromName')->willReturn('Sally Jones');
        $parthenonMessage->method('getToAddress')->willReturn('bob@example.org');
        $parthenonMessage->method('getToName')->willReturn('Bob Johnson');
        $parthenonMessage->method('getSubject')->willReturn('A test email');
        $parthenonMessage->method('getContent')->willReturn('The email body');
        $parthenonMessage->method('isTemplate')->willReturn(false);
        $parthenonMessage->method('getAttachments')->willReturn([]);

        $response->method('getStatusCode')->willReturn(200);

        $mailgun->method('messages')->willReturn($message);
        $message->expects($this->once())->method('send')->with(self::DOMAIN_COM, [
            'from' => 'sally@example.org',
            'to' => 'bob@example.org',
            'subject' => 'A test email',
            'html' => 'The email body',
        ])->willReturn($response);

        $sender = new MailgunEmailSender($mailgun, self::DOMAIN_COM, $configuration);
        $sender->send($parthenonMessage);
    }

    public function testCallsMailgunWithTemplate()
    {
        $mailgun = $this->createMock(Mailgun::class);
        $message = $this->createMock(Message::class);
        $parthenonMessage = $this->createMock(EmailInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $configuration = $this->createMock(Configuration::class);

        $parthenonMessage->method('getFromAddress')->willReturn('sally@example.org');
        $parthenonMessage->method('getFromName')->willReturn('Sally Jones');
        $parthenonMessage->method('getToAddress')->willReturn('bob@example.org');
        $parthenonMessage->method('getToName')->willReturn('Bob Johnson');
        $parthenonMessage->method('getSubject')->willReturn('A test email');
        $parthenonMessage->method('getContent')->willReturn('The email body');
        $parthenonMessage->method('getTemplateName')->willReturn('template_name');
        $parthenonMessage->method('getTemplateVariables')->willReturn([]);
        $parthenonMessage->method('isTemplate')->willReturn(true);
        $parthenonMessage->method('getAttachments')->willReturn([]);

        $response->method('getStatusCode')->willReturn(200);

        $mailgun->method('messages')->willReturn($message);
        $message->expects($this->once())->method('send')->with(self::DOMAIN_COM, [
            'from' => 'sally@example.org',
            'to' => 'bob@example.org',
            'subject' => 'A test email',
            'template' => 'template_name',
            'h:X-Mailgun-Variables' => '[]',
        ])->willReturn($response);

        $sender = new MailgunEmailSender($mailgun, self::DOMAIN_COM, $configuration);
        $sender->send($parthenonMessage);
    }

    public function testCallsMailgunWithTemplateWithVariables()
    {
        $mailgun = $this->createMock(Mailgun::class);
        $message = $this->createMock(Message::class);
        $parthenonMessage = $this->createMock(EmailInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $configuration = $this->createMock(Configuration::class);

        $parthenonMessage->method('getFromAddress')->willReturn('sally@example.org');
        $parthenonMessage->method('getFromName')->willReturn('Sally Jones');
        $parthenonMessage->method('getToAddress')->willReturn('bob@example.org');
        $parthenonMessage->method('getToName')->willReturn('Bob Johnson');
        $parthenonMessage->method('getSubject')->willReturn('A test email');
        $parthenonMessage->method('getContent')->willReturn('The email body');
        $parthenonMessage->method('getTemplateName')->willReturn('template_name');
        $parthenonMessage->method('getTemplateVariables')->willReturn(['name' => 'Test Name']);
        $parthenonMessage->method('isTemplate')->willReturn(true);
        $parthenonMessage->method('getAttachments')->willReturn([]);

        $response->method('getStatusCode')->willReturn(200);

        $mailgun->method('messages')->willReturn($message);
        $message->expects($this->once())->method('send')->with(self::DOMAIN_COM, [
            'from' => 'sally@example.org',
            'to' => 'bob@example.org',
            'subject' => 'A test email',
            'template' => 'template_name',
            'h:X-Mailgun-Variables' => json_encode(['name' => 'Test Name']),
        ])->willReturn($response);

        $sender = new MailgunEmailSender($mailgun, self::DOMAIN_COM, $configuration);
        $sender->send($parthenonMessage);
    }

    public function testCallsMailgunWithTemplateWithVariablesWithAttachment()
    {
        $mailgun = $this->createMock(Mailgun::class);
        $message = $this->createMock(Message::class);
        $parthenonMessage = $this->createMock(EmailInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $attachment = $this->createMock(Attachment::class);
        $configuration = $this->createMock(Configuration::class);

        $attachment->method('getContent')->willReturn('content here');
        $attachment->method('getName')->willReturn('text_file.txt');

        $parthenonMessage->method('getFromAddress')->willReturn('sally@example.org');
        $parthenonMessage->method('getFromName')->willReturn('Sally Jones');
        $parthenonMessage->method('getToAddress')->willReturn('bob@example.org');
        $parthenonMessage->method('getToName')->willReturn('Bob Johnson');
        $parthenonMessage->method('getSubject')->willReturn('A test email');
        $parthenonMessage->method('getContent')->willReturn('The email body');
        $parthenonMessage->method('getTemplateName')->willReturn('template_name');
        $parthenonMessage->method('getTemplateVariables')->willReturn(['name' => 'Test Name']);
        $parthenonMessage->method('isTemplate')->willReturn(true);
        $parthenonMessage->method('getAttachments')->willReturn([$attachment]);

        $response->method('getStatusCode')->willReturn(200);

        $mailgun->method('messages')->willReturn($message);
        $message->expects($this->once())->method('send')->with(self::DOMAIN_COM, [
            'from' => 'sally@example.org',
            'to' => 'bob@example.org',
            'subject' => 'A test email',
            'template' => 'template_name',
            'h:X-Mailgun-Variables' => json_encode(['name' => 'Test Name']),
            'attachment' => [
                ['fileContent' => 'content here', 'filename' => 'text_file.txt'],
            ],
        ])->willReturn($response);

        $sender = new MailgunEmailSender($mailgun, self::DOMAIN_COM, $configuration);
        $sender->send($parthenonMessage);
    }

    public function testCallsMailgunInvalidResponse()
    {
        $this->expectException(UnableToSendMessageException::class);

        $mailgun = $this->createMock(Mailgun::class);
        $message = $this->createMock(Message::class);
        $parthenonMessage = $this->createMock(EmailInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $configuration = $this->createMock(Configuration::class);

        $parthenonMessage->method('getFromAddress')->willReturn('sally@example.org');
        $parthenonMessage->method('getFromName')->willReturn('Sally Jones');
        $parthenonMessage->method('getToAddress')->willReturn('bob@example.org');
        $parthenonMessage->method('getToName')->willReturn('Bob Johnson');
        $parthenonMessage->method('getSubject')->willReturn('A test email');
        $parthenonMessage->method('getContent')->willReturn('The email body');
        $parthenonMessage->method('getTemplateName')->willReturn('template_name');
        $parthenonMessage->method('getTemplateVariables')->willReturn([]);
        $parthenonMessage->method('isTemplate')->willReturn(true);
        $parthenonMessage->method('getAttachments')->willReturn([]);

        $response->method('getStatusCode')->willReturn(403);

        $mailgun->method('messages')->willReturn($message);
        $message->expects($this->once())->method('send')->with(self::DOMAIN_COM, [
            'from' => 'sally@example.org',
            'to' => 'bob@example.org',
            'subject' => 'A test email',
            'template' => 'template_name',
            'h:X-Mailgun-Variables' => '[]',
        ])->willReturn($response);

        $sender = new MailgunEmailSender($mailgun, self::DOMAIN_COM, $configuration);
        $sender->send($parthenonMessage);
    }
}
