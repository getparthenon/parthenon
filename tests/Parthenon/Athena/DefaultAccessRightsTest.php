<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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

        $expected = ['create' => User::DEFAULT_ROLE, 'view' => User::DEFAULT_ROLE, 'delete' => User::DEFAULT_ROLE, 'edit' => User::DEFAULT_ROLE, 'export' => User::DEFAULT_ROLE];

        $subject = new DefaultAccessRights();
        $this->assertEquals($expected, $subject->getAccessRights($section));
    }

    public function testReturnsRoleModified()
    {
        $section = $this->createMock(SectionInterface::class);
        $section->method('getAccessRights')->willReturn(['view' => 'ROLE_ADMIN']);

        $expected = ['create' => User::DEFAULT_ROLE, 'view' => 'ROLE_ADMIN', 'delete' => User::DEFAULT_ROLE, 'edit' => User::DEFAULT_ROLE, 'export' => User::DEFAULT_ROLE];

        $subject = new DefaultAccessRights();
        $this->assertEquals($expected, $subject->getAccessRights($section));
    }
}
