<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification;

interface EmailInterface
{
    public function getSubject();

    public function getFromName(): ?string;

    public function getFromAddress(): ?string;

    public function getToName();

    public function getToAddress();

    public function getContent();

    public function isTemplate(): bool;

    public function getTemplateName(): string;

    public function getTemplateVariables(): array;

    /**
     * @return Attachment[]
     */
    public function getAttachments(): array;
}
