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
