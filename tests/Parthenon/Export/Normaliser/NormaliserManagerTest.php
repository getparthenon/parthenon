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

namespace Parthenon\Export\Normaliser;

use Parthenon\Export\Exception\NoNormaliserFoundException;
use PHPUnit\Framework\TestCase;

class NormaliserManagerTest extends TestCase
{
    public function testGetNormaliser()
    {
        $item = new \stdClass();

        $normaliser = $this->createMock(NormaliserInterface::class);
        $normaliser->method('supports')->with($item)->willReturn(true);

        $subject = new NormaliserManager();
        $subject->addNormaliser($normaliser);

        $actual = $subject->getNormaliser($item);
        $this->assertSame($normaliser, $actual);
    }

    public function testFailedNormaliser()
    {
        $this->expectException(NoNormaliserFoundException::class);
        $item = new \stdClass();

        $normaliser = $this->createMock(NormaliserInterface::class);
        $normaliser->method('supports')->with($item)->willReturn(false);

        $subject = new NormaliserManager();
        $subject->addNormaliser($normaliser);

        $subject->getNormaliser($item);
    }

    public function testGetNormaliserCorrectOne()
    {
        $item = new \stdClass();

        $normaliserNotOne = $this->createMock(NormaliserInterface::class);
        $normaliserNotOne->method('supports')->with($item)->willReturn(false);
        $normaliserNotTwo = $this->createMock(NormaliserInterface::class);
        $normaliserNotTwo->method('supports')->with($item)->willReturn(false);

        $normaliser = $this->createMock(NormaliserInterface::class);
        $normaliser->method('supports')->with($item)->willReturn(true);

        $subject = new NormaliserManager();
        $subject->addNormaliser($normaliserNotOne);
        $subject->addNormaliser($normaliserNotTwo);
        $subject->addNormaliser($normaliser);

        $actual = $subject->getNormaliser($item);
        $this->assertSame($normaliser, $actual);
    }
}
