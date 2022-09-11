<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Decider\EnabledDecider;

use Parthenon\AbTesting\Decider\EnabledDeciderInterface;
use PHPUnit\Framework\TestCase;

class DeciderManagerTest extends TestCase
{
    public function testReturnsTrueByDefault()
    {
        $deciderManager = new DeciderManager();
        $this->assertTrue($deciderManager->isTestable());
    }

    public function testReturnsFalseWhenEnablerIsFalse()
    {
        $enabler = $this->createMock(EnabledDeciderInterface::class);

        $enabler->expects($this->once())
            ->method('isTestable')
            ->willReturn(false);

        $deciderManager = new DeciderManager();
        $deciderManager->add($enabler);
        $this->assertFalse($deciderManager->isTestable());
    }

    public function testReturnsTrueWhenEnablerIsTrue()
    {
        $enabler = $this->createMock(EnabledDeciderInterface::class);

        $enabler->expects($this->once())
            ->method('isTestable')
            ->willReturn(true);

        $deciderManager = new DeciderManager();
        $deciderManager->add($enabler);
        $this->assertTrue($deciderManager->isTestable());
    }

    public function testReturnsTrueWhenEnablerIsTrueUsesInnerCache()
    {
        $enabler = $this->createMock(EnabledDeciderInterface::class);

        $enabler->expects($this->once())
            ->method('isTestable')
            ->willReturn(true);

        $deciderManager = new DeciderManager();
        $deciderManager->add($enabler);
        $this->assertTrue($deciderManager->isTestable());
        $this->assertTrue($deciderManager->isTestable());
    }
}
