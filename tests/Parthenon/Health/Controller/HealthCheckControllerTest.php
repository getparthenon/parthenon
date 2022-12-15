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

namespace Parthenon\Health\Controller;

use Parthenon\Health\Checks\CheckInterface;
use Parthenon\Health\Checks\CheckManager;
use PHPUnit\Framework\TestCase;

class HealthCheckControllerTest extends TestCase
{
    public function testIsHealthy()
    {
        $checkManager = $this->createMock(CheckManager::class);
        $checkOne = $this->createMock(CheckInterface::class);
        $checkTwo = $this->createMock(CheckInterface::class);

        $checkManager->method('getChecks')->willReturn([$checkOne, $checkTwo]);

        $checkOne->method('getName')->willReturn('check_one');
        $checkOne->method('getStatus')->willReturn(true);
        $checkTwo->method('getName')->willReturn('check_two');
        $checkTwo->method('getStatus')->willReturn(true);

        $controller = new HealthcheckController();
        $response = $controller->health($checkManager);

        $data = json_decode($response->getContent(), true);

        $this->assertTrue($data['isHealthy']);
    }

    public function testIsNotHealthy()
    {
        $checkManager = $this->createMock(CheckManager::class);
        $checkOne = $this->createMock(CheckInterface::class);
        $checkTwo = $this->createMock(CheckInterface::class);

        $checkManager->method('getChecks')->willReturn([$checkOne, $checkTwo]);

        $checkOne->method('getName')->willReturn('check_one');
        $checkOne->method('getStatus')->willReturn(false);
        $checkTwo->method('getName')->willReturn('check_two');
        $checkTwo->method('getStatus')->willReturn(true);

        $controller = new HealthcheckController();
        $response = $controller->health($checkManager);

        $data = json_decode($response->getContent(), true);

        $this->assertFalse($data['isHealthy']);
    }
}
