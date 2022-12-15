<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Notification\Sender;

use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailInterface;
use PHPUnit\Framework\TestCase;
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
        }));

        $sender = new PostmarkEmailSender($postmarkClient);
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
        }));

        $sender = new PostmarkEmailSender($postmarkClient);
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
        }));

        $sender = new PostmarkEmailSender($postmarkClient);
        $sender->send($message);
    }
}
