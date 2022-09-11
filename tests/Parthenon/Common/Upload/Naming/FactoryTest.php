<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload\Naming;

use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testReturnsMd5()
    {
        $factory = new Factory();
        $this->assertInstanceOf(NamingMd5Time::class, $factory->getStrategy(NamingStrategyInterface::MD5_TIME));
    }

    public function testReturnsRandom()
    {
        $factory = new Factory();
        $this->assertInstanceOf(RandomTime::class, $factory->getStrategy(NamingStrategyInterface::RANDOM_TIME));
    }
}
