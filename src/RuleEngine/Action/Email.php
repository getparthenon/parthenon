<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Action;

use Parthenon\Notification\Email as EmailNotification;
use Parthenon\Notification\EmailSenderInterface;

final class Email implements ActionInterface
{
    use ExtractVariablesTrait;

    private EmailSenderInterface $sender;

    public function __construct(EmailSenderInterface $sender)
    {
        $this->sender = $sender;
    }

    public function getName(): string
    {
        return 'Email';
    }

    public function getOptions(): array
    {
        return [
            [
                'name' => 'address',
                'type' => 'email',
                'label' => 'E-mail Address',
            ],
            [
                'name' => 'body',
                'type' => 'textarea',
                'label' => 'Body',
                'info' => 'This is only used if template is not given. To insert values from the entity {{ entity.fieldName }} with fieldName being the same as field being used.',
            ],
            [
                'name' => 'temaple',
                'type' => 'text',
                'label' => 'Template',
            ],
            [
                'label' => 'Subject',
                'name' => 'subject',
                'type' => 'text',
            ],
         ];
    }

    public function execute(array $options, $entity): void
    {
        $message = new EmailNotification();
        $message->setToAddress($options['email'])
            ->setSubject($options['subject'])
            ->setTemplateVariables($this->getVariableData($entity));

        if (isset($options['template']) && !empty($options['template'])) {
            $message->setTemplateName($options['template']);
        } elseif (isset($options['body']) && !empty($options['body'])) {
            $body = $options['body'];
            foreach ($message->getTemplateVariables() as $key => $value) {
                if (is_array($value)) {
                    $value = implode(',', $value);
                } else {
                    $value = (string) $value;
                }

                $body = str_replace('{{ '.$key.' }}', $value, $body);
                $body = str_replace('{{'.$key.'}}', $value, $body);
            }

            $message->setContent($body);
        } else {
            throw new \InvalidArgumentException('Template or body need to be set');
        }

        $this->sender->send($message);
    }
}
