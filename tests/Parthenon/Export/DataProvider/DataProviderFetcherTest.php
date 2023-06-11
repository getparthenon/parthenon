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
