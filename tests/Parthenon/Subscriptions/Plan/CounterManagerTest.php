<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Plan;

use PHPUnit\Framework\TestCase;

class CounterManagerTest extends TestCase
{
    public function testReturnsCounter()
    {
        $counter = $this->createMock(CounterInterface::class);
        $limitable = $this->createMock(LimitableInterface::class);

        $counter->method('supports')->with($limitable)->willReturn(true);

        $manager = new CounterManager();
        $manager->add($counter);

        $this->assertEquals($counter, $manager->getCounter($limitable));
    }
}
