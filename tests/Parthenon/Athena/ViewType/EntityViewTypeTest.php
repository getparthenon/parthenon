<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
        $entity = new class() implements CrudEntityInterface {
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
