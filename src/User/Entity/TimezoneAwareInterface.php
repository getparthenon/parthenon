<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Entity;

interface TimezoneAwareInterface
{
    public function getTimezone(): \DateTimeZone;

    public function hasTimezone(): bool;
}
