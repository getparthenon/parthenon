<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
