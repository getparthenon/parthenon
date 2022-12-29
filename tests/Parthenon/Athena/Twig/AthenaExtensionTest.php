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

namespace Parthenon\Athena\Twig;

use Parthenon\Athena\Filters\FilterInterface;
use Parthenon\Athena\Filters\ListFiltersInterface;
use Parthenon\Athena\Repository\NotificationRepositoryInterface;
use Parthenon\Athena\SectionManager;
use PHPUnit\Framework\TestCase;

class AthenaExtensionTest extends TestCase
{
    public function testCreatesQueryString()
    {
        $sectionManager = $this->createMock(SectionManager::class);
        $notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $listFilters = $this->createMock(ListFiltersInterface::class);

        $filterOne = $this->createMock(FilterInterface::class);
        $filterOne->method('hasData')->willReturn(true);
        $filterOne->method('getName')->willReturn('random');
        $filterOne->method('getFieldName')->willReturn('one');
        $filterOne->method('getData')->willReturn('value');

        $filterTwo = $this->createMock(FilterInterface::class);
        $filterTwo->method('hasData')->willReturn(false);
        $filterTwo->expects($this->never())->method('getName')->willReturn('none');
        $filterTwo->expects($this->never())->method('getFieldName')->willReturn('invalid');

        $listFilters->method('getFilters')->willReturn([$filterOne, $filterTwo]);

        $ext = new AthenaExtension($sectionManager, $notificationRepository, '', '');
        $this->assertEquals('&filters[one]=value', $ext->generateQueryString($listFilters));
    }
}
