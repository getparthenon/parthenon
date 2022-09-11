<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Action;

use Parthenon\Athena\Entity\Link;
use Parthenon\Athena\NotifierInterface;

final class AthenaNotification implements ActionInterface
{
    use ExtractVariablesTrait;

    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function getName(): string
    {
        return 'Athena Notification';
    }

    public function getOptions(): array
    {
        return [
            [
                'name' => 'message_template',
                'type' => 'text',
                'label' => 'Message',
            ],
            [
                'name' => 'url_template',
                'type' => 'text',
                'label' => 'URL',
            ],
        ];
    }

    public function execute(array $options, $entity): void
    {
        $messageTemplate = $options['message_template'];
        $urlTemplate = $options['url_template'];
        foreach ($this->getVariableData($entity) as $key => $value) {
            [, $key] = explode('.', $key);
            $messageTemplate = str_replace('{'.$key.'}', (string) $value, $messageTemplate);
            $urlTemplate = str_replace('{'.$key.'}', (string) $value, $urlTemplate);
        }

        $this->notifier->notify($messageTemplate, new Link($urlTemplate, [], true));
    }
}
