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
        $exportRequest->method('getName')->willreturn('random-export');

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
        $exportRequest->method('getName')->willreturn('random-export');

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
