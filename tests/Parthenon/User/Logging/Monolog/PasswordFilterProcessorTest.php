<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Logging\Monolog;

use PHPUnit\Framework\TestCase;

class PasswordFilterProcessorTest extends TestCase
{
    public function testRemovesPassword()
    {
        $passwordFilter = new PasswordFilterProcessor();

        $actual = $passwordFilter(['context' => ['password' => 'a-password']]);
        $expected = ['context' => ['password' => '****']];

        $this->assertEquals($expected, $actual);
    }

    public function testRemovesPasswordDeep()
    {
        $passwordFilter = new PasswordFilterProcessor();

        $actual = $passwordFilter(['context' => ['subcontext' => ['password' => 'a-password']]]);
        $expected = ['context' => ['subcontext' => ['password' => '****']]];

        $this->assertEquals($expected, $actual);
    }
}
