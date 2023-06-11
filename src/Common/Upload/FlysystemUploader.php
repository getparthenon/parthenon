<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common\Upload;

use League\Flysystem\FilesystemOperator;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\Upload\Naming\NamingStrategyInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FlysystemUploader implements UploaderInterface
{
    private FilesystemOperator $filesystem;

    private NamingStrategyInterface $namingStrategy;
    private string $url;

    public function __construct(FilesystemOperator $filesystem, NamingStrategyInterface $namingStrategy, string $url)
    {
        $this->filesystem = $filesystem;
        $this->namingStrategy = $namingStrategy;
        $this->url = $url;
    }

    public function uploadUploadedFile(UploadedFile $file): File
    {
        try {
            $content = file_get_contents($file->getRealPath());
            $filename = $this->namingStrategy->getName($file->getClientOriginalName());
            $this->filesystem->write($filename, $content);

            return new File(rtrim($this->url, '/').'/'.$filename, $filename);
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function deleteFile(File $file): void
    {
        try {
            $this->filesystem->delete($file->getFilename());
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function readFile(File $file)
    {
        try {
            return $this->filesystem->readStream($file->getFilename());
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function uploadString(string $filename, string $contents): File
    {
        try {
            $filename = $this->namingStrategy->getName($filename);
            $this->filesystem->write($filename, $contents);

            return new File(rtrim($this->url, '/').'/'.$filename, $filename);
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
