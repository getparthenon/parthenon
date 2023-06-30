<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Routing;

use Parthenon\Athena\SectionInterface;
use Parthenon\Athena\SectionManager;
use PHPUnit\Framework\TestCase;

class AthenaRouteLoaderTest extends TestCase
{
    public function testReturnsRoutesWith9Routes()
    {
        $sectionManager = $this->createMock(SectionManager::class);
        $sections = $this->createMock(SectionInterface::class);
        $sections->method('getUrlTag')->willReturn('summy');
        $sectionManager->method('getSections')->willReturn([$sections]);

        $athenaRouteLoader = new AthenaRouteLoader($sectionManager, null);
        $routes = $athenaRouteLoader->load('mixed');
        $this->assertCount(12, $routes);
    }
}
