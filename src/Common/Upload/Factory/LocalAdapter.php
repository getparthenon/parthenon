<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload\Factory;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Parthenon\Common\Exception\Upload\InvalidUploadConfigurationException;

class LocalAdapter implements LocalAdapterInterface
{
    public function build(array $config): LocalFilesystemAdapter
    {
        if (!isset($config['local']['path'])) {
            throw new InvalidUploadConfigurationException('Path is required for local');
        }

        return new LocalFilesystemAdapter($config['local']['path']);
    }
}
