<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
