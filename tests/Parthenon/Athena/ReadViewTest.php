<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Exception\NoSectionOpenException;
use Parthenon\Athena\Exception\SectionAlreadyOpenException;
use Parthenon\Athena\Read\Section;
use Parthenon\Athena\ViewType\TextViewType;
use PHPUnit\Framework\TestCase;

class ReadViewTest extends TestCase
{
    public function testThrowsExceptionAlreadyOpen()
    {
        $this->expectException(SectionAlreadyOpenException::class);

        $viewTypeManager = $this->createMock(ViewTypeManagerInterface::class);
        $viewTypeManager->method('get')->with('text')->willReturnOnConsecutiveCalls(new TextViewType(), new TextViewType());

        $entityForm = new ReadView($viewTypeManager);
        $entityForm->section('test')->section('sub_section');
    }

    public function testNoSectionOpen()
    {
        $this->expectException(NoSectionOpenException::class);

        $viewTypeManager = $this->createMock(ViewTypeManagerInterface::class);
        $viewTypeManager->method('get')->with('text')->willReturnOnConsecutiveCalls(new TextViewType(), new TextViewType());

        $entityForm = new ReadView($viewTypeManager);
        $entityForm->field('field');
    }

    public function testReturnsListOfSections()
    {
        $viewTypeManager = $this->createMock(ViewTypeManagerInterface::class);
        $viewTypeManager->method('get')->with('text')->willReturnOnConsecutiveCalls(new TextViewType(), new TextViewType());

        $entityForm = new ReadView($viewTypeManager);
        $sections = $entityForm->section('SectionName')->end()->section('SectionTwo')->end()->getSections();

        $this->assertContainsOnlyInstancesOf(Section::class, $sections);

        $viewData = [];
        foreach ($sections as $section) {
            $viewData[] = $section->getHeaderName();
        }

        $this->assertEquals(['SectionName', 'SectionTwo'], $viewData);
    }
}
