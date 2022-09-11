<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Health\Checks;

use PHPUnit\Framework\TestCase;

class CheckManagerTest extends TestCase
{
    public function testItReturnsAllChecksAdded()
    {
        $checkOne = $this->createMock(CheckInterface::class);
        $checkTwo = $this->createMock(CheckInterface::class);

        $manager = new CheckManager();
        $manager->addCheck($checkOne)->addCheck($checkTwo);

        $this->assertCount(2, $manager->getChecks());
    }
}
