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

namespace Parthenon\Export\Engine;

use Parthenon\Export\DataProvider\DataProviderFetcherInterface;
use Parthenon\Export\DataProvider\DataProviderInterface;
use Parthenon\Export\Exception\ExportFailedException;
use Parthenon\Export\Exception\InvalidDataProviderException;
use Parthenon\Export\Exporter\ExporterInterface;
use Parthenon\Export\Exporter\ExporterManagerInterface;
use Parthenon\Export\ExportRequest;
use Parthenon\Export\Normaliser\NormaliserInterface;
use Parthenon\Export\Normaliser\NormaliserManagerInterface;
use PHPUnit\Framework\TestCase;

class DirectDownloadEngineTest extends TestCase
{
    public function testCallNormaliserThenExporter()
    {
        $exportRequest = $this->createMock(ExportRequest::class);
        $exportRequest->method('getFilename')->willreturn('random-export');

        $exporter = $this->createMock(ExporterInterface::class);
        $exporterManager = $this->createMock(ExporterManagerInterface::class);

        $normaliser = $this->createMock(NormaliserInterface::class);
        $normaliserManager = $this->createMock(NormaliserManagerInterface::class);

        $dataProvider = $this->createMock(DataProviderInterface::class);
        $dataProviderFetcher = $this->createMock(DataProviderFetcherInterface::class);

        $dataProviderFetcher->method('getDataProvider')->with($exportRequest)->willReturn($dataProvider);

        $data = [[0], [1], [2], [3]];
        $dataProvider->method('getData')->with($exportRequest)->willReturn($data);

        $exporterManager->method('getExporter')->with($exportRequest)->willReturn($exporter);

        $normaliserManager->expects($this->once())->method('getNormaliser')->with([0])->willReturn($normaliser);

        $normalisedData = [[4], [5], [6], [7]];
        $normaliser->method('normalise')->with($this->anything())->willReturnOnConsecutiveCalls([4], [5], [6], [7]);

        $exportData = 'Export data';

        $exporter->expects($this->once())->method('getOutput')->with($normalisedData)->willReturn($exportData);

        $subject = new DirectDownloadEngine($normaliserManager, $exporterManager, $dataProviderFetcher);
        $subject->process($exportRequest);
    }

    public function testThrowExceptionInvalidDataProvider()
    {
        $this->expectException(ExportFailedException::class);
        $exportRequest = $this->createMock(ExportRequest::class);
        $exportRequest->method('getFilename')->willreturn('random-export');

        $exporter = $this->createMock(ExporterInterface::class);
        $exporterManager = $this->createMock(ExporterManagerInterface::class);

        $normaliser = $this->createMock(NormaliserInterface::class);
        $normaliserManager = $this->createMock(NormaliserManagerInterface::class);
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $dataProviderFetcher = $this->createMock(DataProviderFetcherInterface::class);

        $dataProviderFetcher->method('getDataProvider')->with($exportRequest)->willThrowException(new InvalidDataProviderException());

        $data = [[0], [1], [2], [3]];
        $dataProvider->method('getData')->with($exportRequest)->willReturn($data);

        $exporterManager->method('getExporter')->with($exportRequest)->willReturn($exporter);

        $normaliserManager->expects($this->never())->method('getNormaliser')->with([0])->willReturn($normaliser);

        $normalisedData = [[4], [5], [6], [7]];
        $normaliser->expects($this->never())->method('normalise')->with($this->anything())->willReturnOnConsecutiveCalls([4], [5], [6], [7]);

        $exportData = 'Export data';

        $exporter->method('getOutput')->with($normalisedData)->willReturn($exportData);

        $subject = new DirectDownloadEngine($normaliserManager, $exporterManager, $dataProviderFetcher);
        $subject->process($exportRequest);
    }
}
