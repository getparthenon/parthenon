<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
