<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload\Factory;

use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;

interface S3AdapterInterface
{
    public function build(array $config): AsyncAwsS3Adapter;
}
