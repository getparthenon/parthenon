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
use League\Flysystem\Local\LocalFilesystemAdapter;
use Parthenon\Common\Exception\Upload\InvalidUploadConfigurationException;
use Parthenon\Common\Exception\Upload\NoUploadProviderFoundException;
use Parthenon\Common\Upload\UploaderInterface;
use PHPUnit\Framework\TestCase;

class FlySystemAdapterFactoryTest extends TestCase
{
    public function testThrowsException()
    {
        $this->expectException(InvalidUploadConfigurationException::class);

        $localAdapter = $this->createMock(LocalAdapterInterface::class);
        $s3Adapter = $this->createMock(S3AdapterInterface::class);

        $flysystemAdpaterFactory = new FlySystemAdapterFactory($s3Adapter, $localAdapter);
        $flysystemAdpaterFactory->getAdapter([]);
    }

    public function testThrowsExceptionInvalidProvider()
    {
        $this->expectException(NoUploadProviderFoundException::class);

        $localAdapter = $this->createMock(LocalAdapterInterface::class);
        $s3Adapter = $this->createMock(S3AdapterInterface::class);

        $flysystemAdpaterFactory = new FlySystemAdapterFactory($s3Adapter, $localAdapter);
        $flysystemAdpaterFactory->getAdapter(['provider' => 'none']);
    }

    public function testCallsS3Adapter()
    {
        $localAdapter = $this->createMock(LocalAdapterInterface::class);
        $s3Adapter = $this->createMock(S3AdapterInterface::class);
        $flySystemS3 = $this->createMock(AsyncAwsS3Adapter::class);

        $config = ['provider' => UploaderInterface::PROVIDER_S3, 's3' => []];

        $s3Adapter->expects($this->once())->method('build')->with($config)->willReturn($flySystemS3);
        $localAdapter->expects($this->never())->method('build')->with($config);

        $flysystemAdpaterFactory = new FlySystemAdapterFactory($s3Adapter, $localAdapter);
        $this->assertInstanceOf(AsyncAwsS3Adapter::class, $flysystemAdpaterFactory->getAdapter($config));
    }

    public function testCallsLocalAdapter()
    {
        $localAdapter = $this->createMock(LocalAdapterInterface::class);
        $s3Adapter = $this->createMock(S3AdapterInterface::class);
        $flySystem = $this->createMock(LocalFilesystemAdapter::class);

        $config = ['provider' => UploaderInterface::PROVIDER_LOCAL, 's3' => []];

        $localAdapter->expects($this->once())->method('build')->with($config)->willReturn($flySystem);
        $s3Adapter->expects($this->never())->method('build')->with($config);

        $flysystemAdpaterFactory = new FlySystemAdapterFactory($s3Adapter, $localAdapter);
        $this->assertInstanceOf(LocalFilesystemAdapter::class, $flysystemAdpaterFactory->getAdapter($config));
    }
}
