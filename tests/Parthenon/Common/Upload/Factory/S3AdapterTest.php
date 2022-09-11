<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
