<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
