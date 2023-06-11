<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common\Naming;

use Parthenon\Common\Upload\Naming\NamingMd5Time;
use PHPUnit\Framework\TestCase;

class NamingMd5TimeTest extends TestCase
{
    public function testReturnsCorrectFileType()
    {
        $namer = new NamingMd5Time();
        $this->assertStringEndsWith('.jpg', $namer->getName('random.jpg'));
        $this->assertStringEndsWith('.pdf', $namer->getName('random.pdf'));
        $this->assertStringEndsWith('.pdf', $namer->getName('random.jpg.pdf'));
        $this->assertStringEndsWith('.png', $namer->getName('random(23).dsds.dfjdkfjf.png'));
    }

    public function testReturnsWithMd5()
    {
        $namer = new NamingMd5Time();
        $this->assertMatchesRegularExpression('~[a-zA-Z0-9]{32}-\d+\.jpg~', $namer->getName('random.jpg'));
    }
}
