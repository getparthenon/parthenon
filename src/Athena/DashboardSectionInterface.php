<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

interface DashboardSectionInterface
{
    public function getTitle(): string;

    public function getTemplate(): string;

    public function getTemplateData(): array;

    public function getColumnSize(): int;

    public function getPriority(): int;

    public function isEnabled(): bool;
}
