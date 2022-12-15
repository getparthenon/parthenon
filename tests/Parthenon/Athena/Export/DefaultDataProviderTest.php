<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Export;

use Parthenon\Athena\Filters\FilterManager;
use Parthenon\Athena\Filters\ListFiltersInterface;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Athena\ResultSet;
use Parthenon\Athena\SectionInterface;
use Parthenon\Athena\SectionManagerInterface;
use Parthenon\Export\Exception\InvalidDataProviderParameterException;
use Parthenon\Export\ExportRequest;
use PHPUnit\Framework\TestCase;

class DefaultDataProviderTest extends TestCase
{
    public function testGetResultFetchAll()
    {
        $output = [1, 2, 3, 4];

        $filterManager = $this->createMock(FilterManager::class);
        $listFilters = $this->createMock(ListFiltersInterface::class);
        $sectionManager = $this->createMock(SectionManagerInterface::class);
        $section = $this->createMock(SectionInterface::class);
        $exportedRequest = $this->createMock(ExportRequest::class);
        $repository = $this->createMock(CrudRepositoryInterface::class);
        $result = $this->createMock(ResultSet::class);

        $sectionTag = 'section-url';

        $parameters = [
            'section_url_tag' => $sectionTag,
            'export_type' => 'all',
            'search' => [],
        ];

        $exportedRequest->method('getDataProviderParameters')->willReturn($parameters);

        $sectionManager->method('getByUrlTag')->with($sectionTag)->willReturn($section);
        $section->method('getRepository')->willReturn($repository);
        $section->method('buildFilters')->willReturn($listFilters);

        $listFilters->method('getFilters')->willReturn([]);

        $repository->method('getList')->with([], 'id', 'asc', -1)->willReturn($result);

        $result->method('getResults')->willReturn($output);

        $subject = new DefaultDataProvider($sectionManager, $filterManager);
        $actual = $subject->getData($exportedRequest);

        $this->assertEquals($output, $actual);
    }

    public function testGetResultByIds()
    {
        $output = [1, 2, 3, 4];

        $filterManager = $this->createMock(FilterManager::class);
        $listFilters = $this->createMock(ListFiltersInterface::class);
        $sectionManager = $this->createMock(SectionManagerInterface::class);
        $section = $this->createMock(SectionInterface::class);
        $exportedRequest = $this->createMock(ExportRequest::class);
        $repository = $this->createMock(CrudRepositoryInterface::class);
        $result = $this->createMock(ResultSet::class);

        $sectionTag = 'section-url';

        $ids = [6, 4, 5];

        $parameters = [
            'section_url_tag' => $sectionTag,
            'export_type' => 'id',
            'search' => $ids,
        ];

        $exportedRequest->method('getDataProviderParameters')->willReturn($parameters);

        $sectionManager->method('getByUrlTag')->with($sectionTag)->willReturn($section);
        $section->method('getRepository')->willReturn($repository);
        $section->method('buildFilters')->willReturn($listFilters);

        $listFilters->method('getFilters')->willReturn([]);

        $repository->method('getByIds')->with($ids)->willReturn($result);

        $result->method('getResults')->willReturn($output);

        $subject = new DefaultDataProvider($sectionManager, $filterManager);
        $actual = $subject->getData($exportedRequest);

        $this->assertEquals($output, $actual);
    }

    public function testCatchInvalidParameterExceptionSectionTag()
    {
        $this->expectException(InvalidDataProviderParameterException::class);

        $filterManager = $this->createMock(FilterManager::class);
        $sectionManager = $this->createMock(SectionManagerInterface::class);
        $exportedRequest = $this->createMock(ExportRequest::class);

        $parameters = [
            'section_url_tag' => null,
            'export_type' => 'id',
            'search' => [],
        ];

        $exportedRequest->method('getDataProviderParameters')->willReturn($parameters);
        $subject = new DefaultDataProvider($sectionManager, $filterManager);
        $subject->getData($exportedRequest);
    }

    public function testCatchInvalidParameterExceptionExportType()
    {
        $this->expectException(InvalidDataProviderParameterException::class);

        $filterManager = $this->createMock(FilterManager::class);
        $sectionManager = $this->createMock(SectionManagerInterface::class);
        $exportedRequest = $this->createMock(ExportRequest::class);

        $parameters = [
            'section_url_tag' => 'null',
            'export_type' => null,
            'search' => [],
        ];

        $exportedRequest->method('getDataProviderParameters')->willReturn($parameters);
        $subject = new DefaultDataProvider($sectionManager, $filterManager);
        $subject->getData($exportedRequest);
    }

    public function testCatchInvalidParameterExceptionSearch()
    {
        $this->expectException(InvalidDataProviderParameterException::class);

        $filterManager = $this->createMock(FilterManager::class);
        $sectionManager = $this->createMock(SectionManagerInterface::class);
        $exportedRequest = $this->createMock(ExportRequest::class);

        $parameters = [
            'section_url_tag' => 'kdkfd',
            'export_type' => 'id',
            'search' => null,
        ];

        $exportedRequest->method('getDataProviderParameters')->willReturn($parameters);
        $subject = new DefaultDataProvider($sectionManager, $filterManager);
        $subject->getData($exportedRequest);
    }
}
