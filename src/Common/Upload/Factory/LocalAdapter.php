<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
