<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
        $this->assertCount(9, $routes);
    }
}
