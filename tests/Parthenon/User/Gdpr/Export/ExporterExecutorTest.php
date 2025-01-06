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
use Symfony\Component\HttpFoundation\Response;

class ExporterExecutorTest extends TestCase
{
    public function testCallsManager()
    {
        $output = ['data' => ['level' => 'two']];

        $exportManager = $this->createMock(ExportManagerInterface::class);
        $formatterManager = $this->createMock(FormatterManagerInterface::class);

        $response = new Response();
        $user = new User();

        $exportManager->method('export')->with($this->equalTo($user))->will($this->returnValue($output));
        $formatterManager->method('format')->with($this->equalTo($user), $this->equalTo($output))->will($this->returnValue($response));

        $exportExecutor = new ExporterExecutor($exportManager, $formatterManager);
        $actual = $exportExecutor->export($user);

        $this->assertEquals($response, $actual);
    }
}
