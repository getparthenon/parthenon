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

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class JsonFormatterTest extends TestCase
{
    public function testReturnsJson()
    {
        $data = ['one' => ['second_level' => 3443, 'third_level' => 43243]];

        $formatter = new JsonFormatter();

        $actual = $formatter->format($data);
        $expected = json_encode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testFileName()
    {
        $user = new User();
        $formatter = new JsonFormatter();
        $this->assertEquals('user-export.json', $formatter->getFilename($user));
    }
}
