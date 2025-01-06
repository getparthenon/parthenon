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

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class ExportManagerTest extends TestCase
{
    public function testCallsExporters()
    {
        $userData = ['name' => 'Sally Johnson'];
        $abData = ['items' => ['one', 'two']];

        $user = new User();

        $exporterOne = $this->createMock(ExporterInterface::class);
        $exporterTwo = $this->createMock(ExporterInterface::class);

        $exporterOne->method('getName')->will($this->returnValue('user'));
        $exporterOne->method('export')->with($this->equalTo($user))->will($this->returnValue($userData));
        $exporterTwo->method('getName')->will($this->returnValue('ab_testing'));
        $exporterTwo->method('export')->with($this->equalTo($user))->will($this->returnValue($abData));

        $expected = ['user' => $userData, 'ab_testing' => $abData];

        $exporterManager = new ExporterManager();
        $exporterManager->add($exporterOne);
        $exporterManager->add($exporterTwo);
        $actual = $exporterManager->export($user);

        $this->assertEquals($expected, $actual);
    }
}
