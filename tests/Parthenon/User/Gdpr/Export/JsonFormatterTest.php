<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class JsonFormatterTest extends TestCase
{
    public function testReturnsJson()
    {
        $data = ['one' => ['second_level' => 3443, 'third_level' => 43243]];

        $formatter = new JsonFormatter();

        $actual = $formatter->format($data);
        $expected = json_encode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testFileName()
    {
        $user = new User();
        $formatter = new JsonFormatter();
        $this->assertEquals('user-export.json', $formatter->getFilename($user));
    }
}
