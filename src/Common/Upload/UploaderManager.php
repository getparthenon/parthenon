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

namespace Parthenon\Common\Upload;

use Parthenon\Common\Exception\Upload\NoUploaderFoundException;
use Parthenon\Common\Upload\Factory\FactoryInterface;

final class UploaderManager implements UploadManagerInterface
{
    public function __construct(private array $configs, private FactoryInterface $factory)
    {
    }

    public function getUploader(string $name = 'default'): UploaderInterface
    {
        if ('default' === $name && 1 === count($this->configs)) {
            $config = current($this->configs);
        } elseif (!isset($this->configs[$name])) {
            throw new NoUploaderFoundException(sprintf('There is no uploader by the name "%s".', $name));
        } else {
            $config = $this->configs[$name];
        }

        return $this->factory->build($config);
    }
}
