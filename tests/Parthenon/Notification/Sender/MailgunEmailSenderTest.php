<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
