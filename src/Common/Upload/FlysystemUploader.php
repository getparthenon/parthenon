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
