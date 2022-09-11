<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload\Naming;

interface FactoryInterface
{
    public function getStrategy(string $name): NamingStrategyInterface;
}
