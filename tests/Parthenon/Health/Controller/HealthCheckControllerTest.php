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
