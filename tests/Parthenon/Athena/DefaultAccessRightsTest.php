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

namespace Parthenon\Athena;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class DefaultAccessRightsTest extends TestCase
{
    public function testReturnsDefaultRules()
    {
        $section = $this->createMock(SectionInterface::class);
        $section->method('getAccessRights')->willReturn([]);

        $expected = ['create' => User::DEFAULT_ROLE, 'view' => User::DEFAULT_ROLE, 'delete' => User::DEFAULT_ROLE, 'edit' => User::DEFAULT_ROLE];

        $subject = new DefaultAccessRights();
        $this->assertEquals($expected, $subject->getAccessRights($section));
    }

    public function testReturnsRoleModified()
    {
        $section = $this->createMock(SectionInterface::class);
        $section->method('getAccessRights')->willReturn(['view' => 'ROLE_ADMIN']);

        $expected = ['create' => User::DEFAULT_ROLE, 'view' => 'ROLE_ADMIN', 'delete' => User::DEFAULT_ROLE, 'edit' => User::DEFAULT_ROLE];

        $subject = new DefaultAccessRights();
        $this->assertEquals($expected, $subject->getAccessRights($section));
    }
}
