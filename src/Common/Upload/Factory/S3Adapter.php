<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload\Factory;

use AsyncAws\S3\S3Client;
use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\Exception\Upload\InvalidUploadConfigurationException;
use Parthenon\Common\Upload\Flysystem\PredefinedVisibilityConverter;

final class S3Adapter implements S3AdapterInterface
{
    public function build(array $config): AsyncAwsS3Adapter
    {
        if (!class_exists(AsyncAwsS3Adapter::class)) {
            throw new GeneralException('AsyncAwsS3Adapter class not found. Run composer require league/flysystem-async-aws-s3');
        }
        if (!isset($config['s3']['key'])) {
            throw new InvalidUploadConfigurationException('Key is required for S3');
        }
        if (!isset($config['s3']['secret'])) {
            throw new InvalidUploadConfigurationException('secret is required for S3');
        }
        if (!isset($config['s3']['region'])) {
            throw new InvalidUploadConfigurationException('region is required for S3');
        }
        if (!isset($config['s3']['endpoint'])) {
            throw new InvalidUploadConfigurationException('endpoint is required for S3');
        }
        if (!isset($config['s3']['bucket_name'])) {
            throw new InvalidUploadConfigurationException('bucket_name is required for S3');
        }
        $visibility = $config['s3']['visibility'] ?? \League\Flysystem\Visibility::PRIVATE;

        $client = new S3Client([
            'accessKeyId' => $config['s3']['key'],
            'accessKeySecret' => $config['s3']['secret'],
            'region' => $config['s3']['region'],
            'endpoint' => $config['s3']['endpoint'],
        ]);

        return new AsyncAwsS3Adapter($client, $config['s3']['bucket_name'], '', new PredefinedVisibilityConverter($visibility));
    }
}
