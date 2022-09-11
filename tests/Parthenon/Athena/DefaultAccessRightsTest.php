<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
