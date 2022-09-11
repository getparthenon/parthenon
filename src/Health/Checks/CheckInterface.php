<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Health\Checks;

interface CheckInterface
{
    public const LEVEL_MINOR = 'minor';
    public const LEVEL_MAJOR = 'major';
    public const LEVEL_CRITICAL = 'critical';

    public function getName(): string;

    public function getLevel(): string;

    public function getStatus(): bool;
}
