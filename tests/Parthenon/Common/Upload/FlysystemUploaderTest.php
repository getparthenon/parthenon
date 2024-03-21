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

namespace Parthenon\Common\Upload;

use League\Flysystem\FilesystemOperator;
use Parthenon\Common\Upload\Naming\NamingStrategyInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FlysystemUploaderTest extends TestCase
{
    public const UPLOAD_URL = 'http://localhost/uploads/';

    public function testCallsFlysytem()
    {
        $flysystem = $this->createMock(FilesystemOperator::class);
        $naming = $this->createMock(NamingStrategyInterface::class);

        $testContent = 'dskjfklsdjflkdsjglskdf'.md5((string) time());
        $tmpName = tempnam('/tmp/', 'test');
        file_put_contents($tmpName, $testContent);

        $originalName = 'item.pdf';
        $newName = 'kdsljflksdjf.pdf';
        $file = new UploadedFile($tmpName, $originalName);

        $naming->method('getName')->with($originalName)->willReturn($newName);
        $flysystem->expects($this->once())->method('write')->with($newName, $testContent);

        $flysystemUploader = new FlysystemUploader($flysystem, $naming, self::UPLOAD_URL);

        $actualFile = $flysystemUploader->uploadUploadedFile($file);

        $this->assertEquals(self::UPLOAD_URL.$newName, $actualFile->getPath());
        $this->assertEquals($newName, $actualFile->getFilename());
        unlink($tmpName);
    }

    public function testCallsDelete()
    {
        $flysystem = $this->createMock(FilesystemOperator::class);
        $naming = $this->createMock(NamingStrategyInterface::class);

        $newName = 'kdsljflksdjf.pdf';
        $file = new File(self::UPLOAD_URL.$newName, $newName);
        $flysystem->expects($this->once())->method('delete')->with($newName);

        $flysystemUploader = new FlysystemUploader($flysystem, $naming, self::UPLOAD_URL);
        $flysystemUploader->deleteFile($file);
    }

    public function testCallsRead()
    {
        $flysystem = $this->createMock(FilesystemOperator::class);
        $naming = $this->createMock(NamingStrategyInterface::class);

        $string = 'Content here....';
        $stream = fopen('data://text/plain,'.$string, 'r');

        $newName = 'kdsljflksdjf.pdf';
        $file = new File(self::UPLOAD_URL.$newName, $newName);
        $flysystem->expects($this->once())->method('readStream')->with($newName)->willReturn($stream);

        $flysystemUploader = new FlysystemUploader($flysystem, $naming, self::UPLOAD_URL);
        $fileContents = $flysystemUploader->readFile($file);
        $this->assertEquals($stream, $fileContents);
    }
}
