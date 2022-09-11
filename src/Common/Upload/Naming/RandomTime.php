<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload\Naming;

final class RandomTime implements NamingStrategyInterface
{
    public function getName(string $filename): string
    {
        $parts = explode('.', $filename);
        $fileType = end($parts);

        return bin2hex(random_bytes(32)).'-'.time().'.'.$fileType;
    }
}
