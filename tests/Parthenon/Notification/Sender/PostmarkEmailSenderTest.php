<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

use Parthenon\Notification\Attachment;
use Parthenon\Notification\Configuration;
use Parthenon\Notification\EmailInterface;
use PHPUnit\Framework\TestCase;
use Postmark\Models\DynamicResponseModel;
use Postmark\PostmarkClient;

class PostmarkEmailSenderTest extends TestCase
{
    public const TO_EMAIL = 'to@example.org';
    public const FROM_EMAIL = 'from@example.org';
    public const SUBJECT = 'Subject';
    public const CONTENT = 'An email from a test';
    public const TEMPLATE_NAME = 'template_name';
    public const TEMPLATE_ARRAY = ['values' => 'value'];

    public function testSendsNonTemplate()
    {
        $postmarkClient = $this->createMock(PostmarkClient::class);
        $message = $this->createMock(EmailInterface::class);

        $message->method('isTemplate')->willReturn(false);
        $message->method('getFromAddress')->willReturn(self::FROM_EMAIL);
        $message->method('getToAddress')->willReturn(self::TO_EMAIL);
        $message->method('getSubject')->willReturn(self::SUBJECT);
        $message->method('getContent')->willReturn(self::CONTENT);
        $message->method('getAttachments')->willReturn([]);

        $postmarkClient->expects($this->once())->method('sendEmailBatch')->with($this->callback(function (array $emails) {
            if (1 != count($emails)) {
                return false;
            }

            $email = $emails[0];

            if (self::TO_EMAIL !== $email['To']) {
                return false;
            }

            if (self::FROM_EMAIL !== $email['From']) {
                return false;
            }

            if (self::SUBJECT !== $email['Subject']) {
                return false;
            }

            if (self::CONTENT !== $email['HtmlBody']) {
                return false;
            }

            return true;
        }))->willReturn(new DynamicResponseModel([['ErrorCode' => 0, 'Message' => 'OK']]));

        $sender = new PostmarkEmailSender($postmarkClient, new Configuration('Test', 'test@example.com'));
        $sender->send($message);
    }

    public function testSendsTemplate()
    {
        $postmarkClient = $this->createMock(PostmarkClient::class);
        $message = $this->createMock(EmailInterface::class);

        $message->method('isTemplate')->willReturn(true);
        $message->method('getFromAddress')->willReturn(self::FROM_EMAIL);
        $message->method('getToAddress')->willReturn(self::TO_EMAIL);
        $message->method('getSubject')->willReturn(self::SUBJECT);
        $message->method('getTemplateName')->willReturn(self::TEMPLATE_NAME);
        $message->method('getTemplateVariables')->willReturn(self::TEMPLATE_ARRAY);
        $message->method('getAttachments')->willReturn([]);

        $postmarkClient->expects($this->once())->method('sendEmailBatchWithTemplate')->with($this->callback(function (array $emails) {
            if (1 != count($emails)) {
                return false;
            }

            $email = $emails[0];

            if (self::TO_EMAIL !== $email['To']) {
                return false;
            }

            if (self::FROM_EMAIL !== $email['From']) {
                return false;
            }

            if (self::SUBJECT !== $email['Subject']) {
                return false;
            }

            if (self::TEMPLATE_NAME !== $email['TemplateAlias']) {
                return false;
            }

            if (self::TEMPLATE_ARRAY !== $email['TemplateModel']) {
                return false;
            }

            return true;
        }))->willReturn(new DynamicResponseModel([['ErrorCode' => 0, 'Message' => 'OK']]));

        $sender = new PostmarkEmailSender($postmarkClient, new Configuration('Test', 'test@example.com'));
        $sender->send($message);
    }

    public function testSendsTemplateWithAttachment()
    {
        $attachment = $this->createMock(Attachment::class);
        $postmarkClient = $this->createMock(PostmarkClient::class);
        $message = $this->createMock(EmailInterface::class);

        $attachment->method('getContent')->willReturn('content here');
        $attachment->method('getName')->willReturn('text_file.txt');

        $message->method('isTemplate')->willReturn(true);
        $message->method('getFromAddress')->willReturn(self::FROM_EMAIL);
        $message->method('getToAddress')->willReturn(self::TO_EMAIL);
        $message->method('getSubject')->willReturn(self::SUBJECT);
        $message->method('getTemplateName')->willReturn(self::TEMPLATE_NAME);
        $message->method('getTemplateVariables')->willReturn(self::TEMPLATE_ARRAY);
        $message->method('getAttachments')->willReturn([$attachment]);

        $postmarkClient->expects($this->once())->method('sendEmailBatchWithTemplate')->with($this->callback(function (array $emails) {
            if (1 != count($emails)) {
                return false;
            }

            $email = $emails[0];

            if (self::TO_EMAIL !== $email['To']) {
                return false;
            }

            if (self::FROM_EMAIL !== $email['From']) {
                return false;
            }

            if (self::SUBJECT !== $email['Subject']) {
                return false;
            }

            if (self::TEMPLATE_NAME !== $email['TemplateAlias']) {
                return false;
            }

            if (self::TEMPLATE_ARRAY !== $email['TemplateModel']) {
                return false;
            }

            return true;
        }))->willReturn(new DynamicResponseModel([['ErrorCode' => 0, 'Message' => 'OK']]));

        $sender = new PostmarkEmailSender($postmarkClient, new Configuration('Test', 'test@example.com'));
        $sender->send($message);
    }
}
