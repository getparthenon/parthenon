<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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
