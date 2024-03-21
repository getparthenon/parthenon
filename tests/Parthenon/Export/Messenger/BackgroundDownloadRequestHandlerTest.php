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

namespace Parthenon\Export\Messenger;

use Parthenon\Common\Upload\File;
use Parthenon\Common\Upload\UploaderInterface;
use Parthenon\Export\DataProvider\DataProviderFetcherInterface;
use Parthenon\Export\DataProvider\DataProviderInterface;
use Parthenon\Export\Entity\BackgroundExportRequest;
use Parthenon\Export\Exporter\ExporterInterface;
use Parthenon\Export\Exporter\ExporterManagerInterface;
use Parthenon\Export\Normaliser\NormaliserInterface;
use Parthenon\Export\Normaliser\NormaliserManagerInterface;
use Parthenon\Export\Repository\BackgroundExportRequestRepositoryInterface;
use PHPUnit\Framework\TestCase;

class BackgroundDownloadRequestHandlerTest extends TestCase
{
    public function testUpload()
    {
        $objectOne = new \stdClass();
        $objectTwo = new \stdClass();
        $data = [$objectOne, $objectTwo];
        $rowOne = [5, 6];
        $rowTwo = [7, 8];
        $expectedOutput = [$rowOne, $rowTwo];
        $exportOutput = 'output';

        $backgroundExport = $this->createMock(BackgroundExportRequest::class);

        $dataProviderFetcher = $this->createMock(DataProviderFetcherInterface::class);
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $exportManagerInterface = $this->createMock(ExporterManagerInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);
        $normaliserManager = $this->createMock(NormaliserManagerInterface::class);
        $normaliser = $this->createMock(NormaliserInterface::class);
        $backgroundRespository = $this->createMock(BackgroundExportRequestRepositoryInterface::class);
        $uploader = $this->createMock(UploaderInterface::class);
        $file = $this->createMock(File::class);

        $id = 'a-id-here';
        $filename = 'export_20203';
        $finalFilename = $filename.'.csv';

        $fileFilename = 'uploaded_file.csv';
        $fileFilepath = 'http://upload.com/upload_file.csv';

        $file->method('getPath')->willReturn($fileFilepath);
        $file->method('getFilename')->willReturn($fileFilename);

        $backgroundExport->method('getId')->willReturn($id);
        $backgroundExport->method('getName')->willReturn($filename);
        $backgroundExport->method('setExportedFile')->with($fileFilename);
        $backgroundExport->method('setExportedFilePath')->with($fileFilepath);

        $dataProviderFetcher->method('getDataProvider')->willReturn($dataProvider);
        $dataProvider->method('getData')->with($backgroundExport)->willReturn($data);

        $normaliserManager->expects($this->once())->method('getNormaliser')->with($objectOne)->willReturn($normaliser);
        $normaliser->method('normalise')->willReturn($rowOne, $rowTwo);

        $exportManagerInterface->method('getExporter')->willReturn($exporter);
        $exporter->method('getOutput')->with($expectedOutput)->willReturn($exportOutput);
        $exporter->method('getFilename')->with($filename)->willReturn($finalFilename);

        $uploader->expects($this->once())->method('uploadString')->with($finalFilename, $exportOutput)->willReturn($file);

        $backgroundRespository->method('findById')->with($id)->willReturn($backgroundExport);
        $backgroundRespository->expects($this->once())->method('save')->with($backgroundExport);

        $subject = new BackgroundDownloadRequestHandler($backgroundRespository, $dataProviderFetcher, $exportManagerInterface, $normaliserManager, $uploader);
        $subject->__invoke($backgroundExport);
    }
}
