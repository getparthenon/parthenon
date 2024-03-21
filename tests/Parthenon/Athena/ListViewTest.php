<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\ViewType\TextViewType;
use Parthenon\Athena\ViewType\ViewTypeInterface;
use PHPUnit\Framework\TestCase;

class ListViewTest extends TestCase
{
    public const NAME = 'IAIN';
    public const TELEPHONE = '01505 566 555';

    public function testShouldReturnHeaders()
    {
        $viewTypeManager = $this->createMock(ViewTypeManagerInterface::class);
        $viewTypeManager->method('get')->with('text')->willReturn(new TextViewType());

        $listView = new ListView($viewTypeManager);
        $headers = $listView->addField('name', 'text')->addField('main_telephone', 'text')->getHeaders();

        $this->assertEquals(['Name', 'Main Telephone'], $listView->getHeaders());
    }

    public function testShouldReturnCorrectDataStdClassSnakeCase()
    {
        $data = new \stdClass();
        $data->name = self::NAME;
        $data->main_telephone = self::TELEPHONE;

        $viewTypeManager = $this->createMock(ViewTypeManagerInterface::class);
        $viewTypeManager->method('get')->with('text')->willReturnOnConsecutiveCalls(new TextViewType(), new TextViewType());

        $listView = new ListView($viewTypeManager);
        $fields = $listView->addField('name', 'text')->addField('main_telephone', 'text')->getData($data);

        $this->assertContainsOnlyInstancesOf(ViewTypeInterface::class, $fields);

        $viewData = [];
        foreach ($fields as $field) {
            $viewData[] = $field->getHtmlOutput();
        }

        $this->assertEquals([self::NAME, self::TELEPHONE], $viewData);
    }

    public function testShouldReturnCorrectDataStdClassCamelCase()
    {
        $data = new \stdClass();
        $data->name = self::NAME;
        $data->mainTelephone = self::TELEPHONE;

        $viewTypeManager = $this->createMock(ViewTypeManagerInterface::class);
        $viewTypeManager->method('get')->with('text')->willReturnOnConsecutiveCalls(new TextViewType(), new TextViewType());

        $listView = new ListView($viewTypeManager);
        $fields = $listView->addField('name', 'text')->addField('main_telephone', 'text')->getData($data);

        $this->assertContainsOnlyInstancesOf(ViewTypeInterface::class, $fields);

        $viewData = [];
        foreach ($fields as $field) {
            $viewData[] = $field->getHtmlOutput();
        }

        $this->assertEquals([self::NAME, self::TELEPHONE], $viewData);
    }

    public function testShouldReturnCorrectDataMethodClassCamelCase()
    {
        $data = new class(self::NAME, self::TELEPHONE) {
            private $name;
            private $telephone;

            public function __construct($name, $telephone)
            {
                $this->name = $name;
                $this->telephone = $telephone;
            }

            public function name()
            {
                return $this->name;
            }

            public function main_telephone()
            {
                return $this->telephone;
            }
        };

        $viewTypeManager = $this->createMock(ViewTypeManagerInterface::class);
        $viewTypeManager->method('get')->with('text')->willReturnOnConsecutiveCalls(new TextViewType(), new TextViewType());

        $listView = new ListView($viewTypeManager);
        $fields = $listView->addField('name', 'text')->addField('main_telephone', 'text')->getData($data);

        $this->assertContainsOnlyInstancesOf(ViewTypeInterface::class, $fields);

        $viewData = [];
        foreach ($fields as $field) {
            $viewData[] = $field->getHtmlOutput();
        }

        $this->assertEquals([self::NAME, self::TELEPHONE], $viewData);
    }

    public function testShouldReturnCorrectDataMethodCamelCase()
    {
        $data = new class(self::NAME, self::TELEPHONE) {
            private $name;
            private $telephone;

            public function __construct($name, $telephone)
            {
                $this->name = $name;
                $this->telephone = $telephone;
            }

            public function name()
            {
                return $this->name;
            }

            public function mainTelephone()
            {
                return $this->telephone;
            }
        };

        $viewTypeManager = $this->createMock(ViewTypeManagerInterface::class);
        $viewTypeManager->method('get')->with('text')->willReturnOnConsecutiveCalls(new TextViewType(), new TextViewType());

        $listView = new ListView($viewTypeManager);
        $fields = $listView->addField('name', 'text')->addField('main_telephone', 'text')->getData($data);

        $this->assertContainsOnlyInstancesOf(ViewTypeInterface::class, $fields);

        $viewData = [];
        foreach ($fields as $field) {
            $viewData[] = $field->getHtmlOutput();
        }

        $this->assertEquals([self::NAME, self::TELEPHONE], $viewData);
    }

    public function testShouldReturnCorrectDataGetterMethodCamelCase()
    {
        $data = new class(self::NAME, self::TELEPHONE) {
            private $name;
            private $telephone;

            public function __construct($name, $telephone)
            {
                $this->name = $name;
                $this->telephone = $telephone;
            }

            public function getName()
            {
                return $this->name;
            }

            public function getMainTelephone()
            {
                return $this->telephone;
            }
        };

        $viewTypeManager = $this->createMock(ViewTypeManagerInterface::class);
        $viewTypeManager->method('get')->with('text')->willReturnOnConsecutiveCalls(new TextViewType(), new TextViewType());

        $listView = new ListView($viewTypeManager);
        $fields = $listView->addField('name', 'text')->addField('main_telephone', 'text')->getData($data);

        $this->assertContainsOnlyInstancesOf(ViewTypeInterface::class, $fields);

        $viewData = [];
        foreach ($fields as $field) {
            $viewData[] = $field->getHtmlOutput();
        }

        $this->assertEquals([self::NAME, self::TELEPHONE], $viewData);
    }
}
