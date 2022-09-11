<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Formatter;

use Parthenon\User\Entity\User;

interface UserFormatterInterface
{
    public function format(User $user): array;
}
