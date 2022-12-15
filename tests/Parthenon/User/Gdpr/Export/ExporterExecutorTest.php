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
