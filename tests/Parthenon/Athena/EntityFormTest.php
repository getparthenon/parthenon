<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Edit\Section;
use Parthenon\Athena\Exception\NoSectionOpenException;
use Parthenon\Athena\Exception\SectionAlreadyOpenException;
use PHPUnit\Framework\TestCase;

class EntityFormTest extends TestCase
{
    public function testThrowsExceptionAlreadyOpen()
    {
        $this->expectException(SectionAlreadyOpenException::class);

        $entityForm = new EntityForm();
        $entityForm->section('test')->section('sub_section');
    }

    public function testNoSectionOpen()
    {
        $this->expectException(NoSectionOpenException::class);

        $entityForm = new EntityForm();
        $entityForm->field('field');
    }

    public function testReturnsListOfSections()
    {
        $entityForm = new EntityForm();
        $sections = $entityForm->section('SectionName')->end()->section('SectionTwo')->end()->getSections();

        $this->assertContainsOnlyInstancesOf(Section::class, $sections);

        $viewData = [];
        foreach ($sections as $section) {
            $viewData[] = $section->getHeaderName();
        }

        $this->assertEquals(['SectionName', 'SectionTwo'], $viewData);
    }
}
