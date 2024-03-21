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

use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use Parthenon\Common\Upload\FlysystemUploader;
use Parthenon\Common\Upload\Naming\Factory;
use Parthenon\Common\Upload\Naming\RandomTime;
use PHPUnit\Framework\TestCase;

class FlysystemFactoryTest extends TestCase
{
    public function testCallsAwsFactory()
    {
        $flysystemFactory = $this->createMock(FlySystemAdapterFactoryInterface::class);
        $namingFactory = $this->createMock(Factory::class);
        $awsAdapter = $this->createMock(AsyncAwsS3Adapter::class);

        $config = ['provider' => 's3',  'naming_strategy' => 'random_time', 'url' => 'url'];

        $flysystemFactory->method('getAdapter')->with($config)->willReturn($awsAdapter);
        $namingFactory->method('getStrategy')->with('random_time')->willReturn(new RandomTime());

        $factory = new FlysystemFactory($flysystemFactory, $namingFactory);
        $this->assertInstanceOf(FlysystemUploader::class, $factory->build($config));
    }
}
