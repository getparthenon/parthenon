<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

use Parthenon\Athena\Entity\Link;
use Parthenon\Athena\Exception\NoSectionFoundException;
use PHPUnit\Framework\TestCase;

class SectionManagerTest extends TestCase
{
    public function testGetByEntity()
    {
        $section = $this->createMock(SectionInterface::class);
        $section->method('getEntity')->willReturn(new Link());

        $accessRightsManager = $this->createMock(AccessRightsManagerInterface::class);

        $sectionManager = new SectionManager($accessRightsManager);
        $sectionManager->addSection($section);

        $actual = $sectionManager->getByEntity(new Link());

        $this->assertSame($section, $actual);
    }

    public function testGetByEntityNotFound()
    {
        $this->expectException(NoSectionFoundException::class);
        $accessRightsManager = $this->createMock(AccessRightsManagerInterface::class);

        $sectionManager = new SectionManager($accessRightsManager);

        $sectionManager->getByEntity(new Link());
    }
}
