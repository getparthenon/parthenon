<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

use League\Flysystem\FilesystemAdapter;
use Parthenon\Common\Exception\Upload\InvalidUploadConfigurationException;
use Parthenon\Common\Exception\Upload\NoUploadProviderFoundException;
use Parthenon\Common\Upload\UploaderInterface;

final class FlySystemAdapterFactory implements FlySystemAdapterFactoryInterface
{
    public function __construct(private S3AdapterInterface $s3Adapter, private LocalAdapterInterface $localAdapter)
    {
    }

    public function getAdapter($config): FilesystemAdapter
    {
        if (!isset($config['provider'])) {
            throw new InvalidUploadConfigurationException('There is no provider defined.');
        }

        switch ($config['provider']) {
            case UploaderInterface::PROVIDER_S3:
                return $this->s3Adapter->build($config);
            case UploaderInterface::PROVIDER_LOCAL:
                return $this->localAdapter->build($config);
            default:
                throw new NoUploadProviderFoundException(sprintf('There is no provider for "%s"', $config['provider']));
        }
    }
}
