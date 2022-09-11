<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload\Naming;

interface NamingStrategyInterface
{
    public const MD5_TIME = 'md5_time';

    public const RANDOM_TIME = 'random_time';

    public function getName(string $filename): string;
}
