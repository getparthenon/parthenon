<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
