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

namespace Parthenon\Common\Logging;

use Monolog\Level;
use Monolog\LogRecord;
use Parthenon\Common\Logging\Monolog\ExceptionProcessor;
use PHPUnit\Framework\TestCase;

class ExceptionProcesorTest extends TestCase
{
    public function testConvertsException()
    {
        $processor = new ExceptionProcessor();

        $exception = new \Exception('message');
        $record = new LogRecord(
            new \DateTimeImmutable(),
            'channel',
            Level::Info,
            'message',
            extra: ['exception' => $exception]);

        $output = $processor->__invoke($record);

        $this->assertArrayHasKey('message', $output['extra']['exception']);
        $this->assertEquals('message', $output['extra']['exception']['message']);
    }

    public function testConvertsExceptionConetxt()
    {
        $processor = new ExceptionProcessor();

        $exception = new \Exception('message');

        $record = new LogRecord(
            new \DateTimeImmutable(),
            'channel',
            Level::Info,
            'message',
            context: ['exception' => $exception]);
        $output = $processor->__invoke($record);

        $this->assertArrayHasKey('message', $output['context']['exception']);
        $this->assertEquals('message', $output['context']['exception']['message']);
    }

    public function testConvertsExceptionConetxtDeep()
    {
        $processor = new ExceptionProcessor();

        $exception = new \Exception('message');
        $record = new LogRecord(
            new \DateTimeImmutable(),
            'channel',
            Level::Info,
            'message',
            context: ['deep' => ['exception' => $exception]]);
        $output = $processor->__invoke($record);

        $this->assertArrayHasKey('message', $output['context']['deep']['exception']);
        $this->assertEquals('message', $output['context']['deep']['exception']['message']);
    }
}
