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

namespace Parthenon\Export\DataProvider;

use Parthenon\Export\Exception\InvalidDataProviderException;
use Parthenon\Export\Exception\NoDataProviderFoundException;
use Parthenon\Export\ExportRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class DataProviderFetcherTest extends TestCase
{
    public function testFetchService()
    {
        $container = $this->createMock(ContainerInterface::class);
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $exportRequest = $this->createMock(ExportRequest::class);

        $serviceId = 'service';

        $exportRequest->method('getDataProviderService')->willReturn($serviceId);
        $container->method('get')->with($serviceId)->willReturn($dataProvider);

        $subject = new DataProviderFetcher($container);

        $actual = $subject->getDataProvider($exportRequest);
        $this->assertSame($dataProvider, $actual);
    }

    public function testFetchServiceNotFound()
    {
        $this->expectException(NoDataProviderFoundException::class);
        $container = $this->createMock(ContainerInterface::class);
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $exportRequest = $this->createMock(ExportRequest::class);

        $serviceId = 'service';

        $exportRequest->method('getDataProviderService')->willReturn($serviceId);
        $container->method('get')->with($serviceId)->willThrowException(new ServiceNotFoundException('Service not found', $serviceId));

        $subject = new DataProviderFetcher($container);

        $actual = $subject->getDataProvider($exportRequest);
    }

    public function testFetchServiceNotValidDataProvider()
    {
        $this->expectException(InvalidDataProviderException::class);
        $container = $this->createMock(ContainerInterface::class);
        $dataProvider = new \stdClass();
        $exportRequest = $this->createMock(ExportRequest::class);

        $serviceId = 'service';

        $exportRequest->method('getDataProviderService')->willReturn($serviceId);
        $container->method('get')->with($serviceId)->willReturn($dataProvider);

        $subject = new DataProviderFetcher($container);

        $actual = $subject->getDataProvider($exportRequest);
    }
}
