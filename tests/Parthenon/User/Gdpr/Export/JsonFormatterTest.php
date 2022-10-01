<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
