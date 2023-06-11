<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Notification\Sender;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\EmailInterface;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\Notification\Exception\UnableToSendMessageException;
use Postmark\Models\PostmarkAttachment;
use Postmark\PostmarkClient;

final class PostmarkEmailSender implements EmailSenderInterface
{
    use LoggerAwareTrait;

    public function __construct(private PostmarkClient $postmarkClient)
    {
    }

    public function send(EmailInterface $message)
    {
        $attachments = [];
        foreach ($message->getAttachments() as $attachment) {
            $attachments[] = PostmarkAttachment::fromRawData($attachment->getContent(), $attachment->getName());
        }

        $email = [
            'To' => $message->getToAddress(),
            'From' => $message->getFromAddress(),
            'Subject' => $message->getSubject(),
            'Attachments' => $attachments,
        ];
        try {
            if ($message->isTemplate()) {
                $email['TemplateAlias'] = $message->getTemplateName();
                $email['TemplateModel'] = $message->getTemplateVariables();
                $response = $this->postmarkClient->sendEmailBatchWithTemplate([$email]);
            } else {
                $email['HtmlBody'] = $message->getContent();
                $response = $this->postmarkClient->sendEmailBatch([$email]);
            }

            $this->getLogger()->info('Sent email using PostMark');
        } catch (\Exception $e) {
            $this->getLogger()->warning('Unable to send email using PostMark', ['exception_message' => $e->getMessage()]);
            throw new UnableToSendMessageException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
