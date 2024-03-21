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
