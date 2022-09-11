<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

abstract class AbstractDashboardSection implements DashboardSectionInterface
{
    abstract public function getTitle(): string;

    abstract public function getTemplate(): string;

    public function getTemplateData(): array
    {
        return [];
    }

    public function getColumnSize(): int
    {
        return 12;
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
