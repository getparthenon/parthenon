<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
