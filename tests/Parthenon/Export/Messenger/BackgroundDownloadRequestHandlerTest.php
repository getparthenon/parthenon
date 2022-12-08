<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
        $backgroundExport->method('getFilename')->willReturn($filename);
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
