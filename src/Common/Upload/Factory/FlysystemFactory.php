<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Common\Upload\Factory;

use League\Flysystem\Filesystem;
use Parthenon\Common\Exception\Upload\InvalidUploadConfigurationException;
use Parthenon\Common\Upload\FlysystemUploader;
use Parthenon\Common\Upload\Naming\Factory as NamingFactory;
use Parthenon\Common\Upload\UploaderInterface;

final class FlysystemFactory implements FactoryInterface
{
    public function __construct(private FlySystemAdapterFactoryInterface $flySystemAdapterFactory, private NamingFactory $factory)
    {
    }

    public function build(array $config): UploaderInterface
    {
        if (!isset($config['provider'])) {
            throw new InvalidUploadConfigurationException('There is no provider defined.');
        }

        if (!isset($config['naming_strategy'])) {
            throw new InvalidUploadConfigurationException('There is no naming_strategy defined.');
        }

        if (!isset($config['url'])) {
            throw new InvalidUploadConfigurationException('There is no url defined.');
        }

        $adapter = $this->flySystemAdapterFactory->getAdapter($config);

        $namingStrategy = $this->factory->getStrategy($config['naming_strategy']);
        $flysystem = new Filesystem($adapter);

        return new FlysystemUploader($flysystem, $namingStrategy, $config['url']);
    }
}
