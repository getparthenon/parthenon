<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
