<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Health;

final class Issue
{
    private string $name;
    private string $level;

    public function __construct(string $name, string $level)
    {
        $this->name = $name;
        $this->level = $level;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLevel(): string
    {
        return $this->level;
    }
}
