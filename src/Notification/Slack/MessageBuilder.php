<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Notification\Slack;

use Parthenon\Notification\Exception\Slack\UnclosedSectionException;
use Parthenon\Notification\Exception\Slack\UnopenedSectionException;

final class MessageBuilder
{
    private $sections = [];

    private $currentSection;

    public function addMarkdownSection(string $text): self
    {
        if (null !== $this->currentSection) {
            throw new UnclosedSectionException();
        }

        $this->currentSection = [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => $text,
            ],
        ];

        return $this;
    }

    public function addTextSection(string $text, bool $emoji = true): self
    {
        if (null !== $this->currentSection) {
            throw new UnclosedSectionException();
        }

        $this->currentSection = [
            'type' => 'section',
            'text' => [
                'type' => 'plain_text',
                'text' => $text,
                'emoji' => $emoji,
            ],
        ];

        return $this;
    }

    public function addImage(string $url, string $altText): self
    {
        if (null === $this->currentSection) {
            throw new UnopenedSectionException();
        }

        $this->currentSection['accessory'] = [
            'type' => 'image',
            'image_url' => $url,
            'alt_text' => $altText,
        ];

        return $this;
    }

    public function addButton(string $text, string $url, string $value = 'click', string $actionId = 'button-action', bool $emoji = true): self
    {
        if (null === $this->currentSection) {
            throw new UnopenedSectionException();
        }
        $this->currentSection['accessory'] = [
            'type' => 'button',
            'text' => [
                'type' => 'plain_text',
                'text' => $text,
                'emoji' => $emoji,
            ],
            'value' => $value,
            'url' => $url,
            'action_id' => $actionId,
        ];

        return $this;
    }

    public function closeSection(): self
    {
        if (null === $this->currentSection) {
            throw new UnopenedSectionException();
        }
        $this->sections[] = $this->currentSection;
        $this->currentSection = null;

        return $this;
    }

    public function addDivider(): self
    {
        if (null !== $this->currentSection) {
            throw new UnclosedSectionException();
        }
        $this->sections[] = ['type' => 'divider'];

        return $this;
    }

    public function build(): array
    {
        if (null !== $this->currentSection) {
            throw new UnclosedSectionException();
        }

        return ['blocks' => $this->sections];
    }
}
