<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common\Logging;

use Parthenon\Common\Logging\Monolog\ExceptionProcessor;
use PHPUnit\Framework\TestCase;

class ExceptionProcesorTest extends TestCase
{
    public function testConvertsException()
    {
        $processor = new ExceptionProcessor();

        $exception = new \Exception('message');
        $record = ['extra' => ['exception' => $exception], 'context' => []];

        $output = $processor->__invoke($record);

        $this->assertArrayHasKey('message', $output['extra']['exception']);
        $this->assertEquals('message', $output['extra']['exception']['message']);
    }

    public function testConvertsExceptionConetxt()
    {
        $processor = new ExceptionProcessor();

        $exception = new \Exception('message');
        $record = ['context' => ['exception' => $exception]];

        $output = $processor->__invoke($record);

        $this->assertArrayHasKey('message', $output['context']['exception']);
        $this->assertEquals('message', $output['context']['exception']['message']);
    }

    public function testConvertsExceptionConetxtDeep()
    {
        $processor = new ExceptionProcessor();

        $exception = new \Exception('message');
        $record = ['context' => ['deep' => ['exception' => $exception]]];

        $output = $processor->__invoke($record);

        $this->assertArrayHasKey('message', $output['context']['deep']['exception']);
        $this->assertEquals('message', $output['context']['deep']['exception']['message']);
    }
}
