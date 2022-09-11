<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload\Factory;

use League\Flysystem\FilesystemAdapter;

interface FlySystemAdapterFactoryInterface
{
    public function getAdapter($config): FilesystemAdapter;
}
