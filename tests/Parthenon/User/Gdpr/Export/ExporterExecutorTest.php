<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
