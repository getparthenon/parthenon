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

namespace Parthenon\Athena\ViewType;

use Parthenon\Athena\Entity\CrudEntityInterface;
use Parthenon\Athena\SectionInterface;
use Parthenon\Athena\SectionManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EntityViewTypeTest extends TestCase
{
    public function testGetHtmlOutput()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $sectionManager = $this->createMock(SectionManagerInterface::class);
        $section = $this->createMock(SectionInterface::class);

        $sectionUrlTag = 'link';
        $section->method('getUrlTag')->willReturn($sectionUrlTag);
        $subject = new EntityViewType($urlGenerator, $sectionManager);

        $entityId = '213004-dsffjds';
        $displayName = 'Entity';
        $entity = new class implements CrudEntityInterface {
            private $id;

            private $name;

            public function setId($id)
            {
                $this->id = $id;
            }

            public function getId()
            {
                return $this->id;
            }

            public function setDisplayName($name)
            {
                $this->name = $name;
            }

            public function getDisplayName(): string
            {
                return $this->name;
            }
        };
        $entity->setId($entityId);
        $entity->setDisplayName($displayName);

        $sectionManager->method('getByEntity')->with($entity)->willReturn($section);
        $linkUrl = '/link/id';
        $urlGenerator->method('generate')->with('parthenon_athena_crud_'.$sectionUrlTag.'_read', ['id' => $entityId])->willReturn($linkUrl);

        $subject->setData($entity);
        $actual = $subject->getHtmlOutput();

        $this->assertEquals(sprintf('<a href="%s">%s</a>', $linkUrl, $displayName), $actual);
    }
}
