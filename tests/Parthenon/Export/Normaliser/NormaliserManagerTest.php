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
