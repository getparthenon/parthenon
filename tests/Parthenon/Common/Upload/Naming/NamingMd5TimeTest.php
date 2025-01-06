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

namespace Parthenon\Common\Naming;

use Parthenon\Common\Upload\Naming\NamingMd5Time;
use PHPUnit\Framework\TestCase;

class NamingMd5TimeTest extends TestCase
{
    public function testReturnsCorrectFileType()
    {
        $namer = new NamingMd5Time();
        $this->assertStringEndsWith('.jpg', $namer->getName('random.jpg'));
        $this->assertStringEndsWith('.pdf', $namer->getName('random.pdf'));
        $this->assertStringEndsWith('.pdf', $namer->getName('random.jpg.pdf'));
        $this->assertStringEndsWith('.png', $namer->getName('random(23).dsds.dfjdkfjf.png'));
    }

    public function testReturnsWithMd5()
    {
        $namer = new NamingMd5Time();
        $this->assertMatchesRegularExpression('~[a-zA-Z0-9]{32}-\d+\.jpg~', $namer->getName('random.jpg'));
    }
}
