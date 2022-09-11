<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload\Naming;

use Parthenon\Common\Exception\Upload\InvalidNamingStrategyException;

final class Factory implements FactoryInterface
{
    public function getStrategy(string $name): NamingStrategyInterface
    {
        switch ($name) {
            case NamingStrategyInterface::MD5_TIME:
                return new NamingMd5Time();
            case NamingStrategyInterface::RANDOM_TIME:
                return new RandomTime();
            default:
                throw new InvalidNamingStrategyException(sprintf("There is no naming strategy '%s'", $name));
        }
    }
}
