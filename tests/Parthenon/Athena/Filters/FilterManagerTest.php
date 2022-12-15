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

namespace Parthenon\Athena\Filters;

use Parthenon\Athena\Exception\InvalidFilterException;
use PHPUnit\Framework\TestCase;

class FilterManagerTest extends TestCase
{
    public function testReturnsContainsFilter()
    {
        $filterManager = new FilterManager();
        $filterManager->add(new ContainsFilter());
        $config = new FilterConfig('field', ContainsFilter::NAME);
        $this->assertInstanceOf(ContainsFilter::class, $filterManager->get($config));
    }

    public function testThowsException()
    {
        $this->expectException(InvalidFilterException::class);

        $filterManager = new FilterManager();
        $config = new FilterConfig('field', ContainsFilter::NAME);
        $filterManager->get($config);
    }
}
