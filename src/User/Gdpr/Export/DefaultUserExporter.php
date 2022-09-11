<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\UserInterface;

final class DefaultUserExporter implements ExporterInterface
{
    public function getName(): string
    {
        return 'user';
    }

    public function export(UserInterface $user): array
    {
        return [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ];
    }
}
