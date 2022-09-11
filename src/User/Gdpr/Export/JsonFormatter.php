<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\UserInterface;

final class JsonFormatter implements FormatterInterface
{
    public function getName(): string
    {
        return 'json';
    }

    public function getFilename(UserInterface $user): string
    {
        return 'user-export.json';
    }

    public function format(array $data): string
    {
        return json_encode($data);
    }
}
