<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
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

namespace Parthenon\Notification;

class Email implements EmailInterface
{
    private ?string $subject = null;

    private ?string $fromAddress = null;

    private ?string $fromName = null;

    private ?string $toAddress = null;

    private ?string $toName = null;

    private ?string $content = null;

    private ?string $templateName = null;

    private array $templateVariables = [];

    /**
     * @var Attachment[]
     */
    private array $attachments = [];

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getFromAddress(): ?string
    {
        return $this->fromAddress;
    }

    public function setFromAddress(?string $fromAddress): self
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function setFromName(?string $fromName): self
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function getToAddress(): ?string
    {
        return $this->toAddress;
    }

    public function setToAddress(?string $toAddress): self
    {
        $this->toAddress = $toAddress;

        return $this;
    }

    public function getToName(): ?string
    {
        return $this->toName;
    }

    public function setToName(?string $toName): self
    {
        $this->toName = $toName;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function setTemplateName(string $templateName): self
    {
        $this->templateName = $templateName;

        return $this;
    }

    public function setTemplateVariables(array $templateVariables): self
    {
        $this->templateVariables = $templateVariables;

        return $this;
    }

    public function getTemplateVariables(): array
    {
        return $this->templateVariables;
    }

    public function isTemplate(): bool
    {
        return !empty($this->templateName);
    }

    public function addAttachment(Attachment $attachment): self
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
