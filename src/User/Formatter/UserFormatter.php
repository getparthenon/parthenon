<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Formatter;

use Parthenon\User\Entity\User;

final class UserFormatter implements UserFormatterInterface
{
    public function format(User $user): array
    {
        return [
            'id' => (string) $user->getId(),
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ];
    }
}
