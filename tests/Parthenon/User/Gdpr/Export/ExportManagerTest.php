<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
