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
use Parthenon\Common\Exception\Upload\InvalidUploadConfigurationException;
use PHPUnit\Framework\TestCase;

class S3AdapterTest extends TestCase
{
    public function testReturnsAdapters()
    {
        $config = ['s3' => ['key' => 'key-value', 'secret' => 'secret-value', 'region' => 'region-value', 'endpoint' => 'endpoint-value', 'bucket_name' => 'bucket_name-value']];

        $adpaterFactory = new S3Adapter();
        $this->assertInstanceOf(AsyncAwsS3Adapter::class, $adpaterFactory->build($config));
    }

    public function testThrowsExceptionNoKey()
    {
        $this->expectException(InvalidUploadConfigurationException::class);
        $config = ['s3' => []];

        $adpaterFactory = new S3Adapter();
        $adpaterFactory->build($config);
    }

    public function testThrowsExceptionNoSecret()
    {
        $this->expectException(InvalidUploadConfigurationException::class);
        $config = ['s3' => ['key' => 'key-value']];

        $adpaterFactory = new S3Adapter();
        $adpaterFactory->build($config);
    }

    public function testThrowsExceptionNoRegion()
    {
        $this->expectException(InvalidUploadConfigurationException::class);
        $config = ['s3' => ['key' => 'key-value', 'secret' => 'secret-value']];

        $adpaterFactory = new S3Adapter();
        $adpaterFactory->build($config);
    }

    public function testThrowsExceptionNoEndpoint()
    {
        $this->expectException(InvalidUploadConfigurationException::class);
        $config = ['s3' => ['key' => 'key-value', 'secret' => 'secret-value', 'region' => 'region-value']];

        $adpaterFactory = new S3Adapter();
        $adpaterFactory->build($config);
    }

    public function testThrowsExceptionNoBucketName()
    {
        $this->expectException(InvalidUploadConfigurationException::class);
        $config = ['s3' => ['key' => 'key-value', 'secret' => 'secret-value', 'region' => 'region-value', 'endpoint' => 'endpoint-value']];

        $adpaterFactory = new S3Adapter();
        $adpaterFactory->build($config);
    }
}
