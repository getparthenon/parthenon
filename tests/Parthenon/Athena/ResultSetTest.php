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

namespace Parthenon\Athena;

use PHPUnit\Framework\TestCase;

class ResultSetTest extends TestCase
{
    public function testReturnsLimitedAmount()
    {
        $itemOne = new \stdClass();
        $itemOne->id = '1';
        $itemTwo = new \stdClass();
        $itemTwo->id = '2';
        $itemThree = new \stdClass();
        $itemThree->id = '3';
        $itemFour = new \stdClass();
        $itemFour->id = '4';
        $data = [$itemOne, $itemTwo, $itemThree, $itemFour];

        $resultSet = new ResultSet($data, 'id', 'desc', 3);
        $this->assertCount(3, $resultSet->getResults());
    }

    public function testReturnsLastId()
    {
        $itemOne = new \stdClass();
        $itemOne->id = '1';
        $itemTwo = new \stdClass();
        $itemTwo->id = '2';
        $itemThree = new \stdClass();
        $itemThree->id = '3';
        $itemFour = new \stdClass();
        $itemFour->id = '4';
        $data = [$itemOne, $itemTwo, $itemThree, $itemFour];

        $resultSet = new ResultSet($data, 'id', 'desc', 3);
        $this->assertEquals(3, $resultSet->getLastKey());
    }

    public function testReturnsHasMore()
    {
        $itemOne = new \stdClass();
        $itemOne->id = '1';
        $itemTwo = new \stdClass();
        $itemTwo->id = '2';
        $itemThree = new \stdClass();
        $itemThree->id = '3';
        $itemFour = new \stdClass();
        $itemFour->id = '4';
        $data = [$itemOne, $itemTwo, $itemThree, $itemFour];

        $resultSet = new ResultSet($data, 'id', 'desc', 3);
        $this->assertTrue($resultSet->hasMore());
    }

    public function testReturnsDoesnotHasMore()
    {
        $itemOne = new \stdClass();
        $itemOne->id = '1';
        $itemTwo = new \stdClass();
        $itemTwo->id = '2';
        $itemThree = new \stdClass();
        $itemThree->id = '3';
        $itemFour = new \stdClass();
        $itemFour->id = '4';
        $data = [$itemOne, $itemTwo, $itemThree, $itemFour];

        $resultSet = new ResultSet($data, 'id', 'desc', 5);
        $this->assertFalse($resultSet->hasMore());
    }
}
