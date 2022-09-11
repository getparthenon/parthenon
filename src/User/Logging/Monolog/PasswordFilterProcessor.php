<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Logging\Monolog;

use Monolog\Processor\ProcessorInterface;

final class PasswordFilterProcessor implements ProcessorInterface
{
    private const PASSWORD_KEY = 'password';

    public function __invoke(array $record): array
    {
        foreach ($record as $key => $item) {
            if (is_string($key) && self::PASSWORD_KEY === strtolower($key)) {
                $record[$key] = '****';
            } elseif (is_array($item)) {
                $record[$key] = $this($item);
            }
        }

        return $record;
    }
}
