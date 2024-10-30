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
