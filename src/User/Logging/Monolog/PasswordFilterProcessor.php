<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
