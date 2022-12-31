<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
