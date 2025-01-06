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

namespace Parthenon\Common\Elasticsearch;

use Parthenon\Common\Elasticsearch\Attributes\Field;
use Parthenon\Common\Exception\Elasticsearch\InvalidAttibuteException;
use PHPUnit\Framework\TestCase;

class AttributesReaderTest extends TestCase
{
    public function testFindsAttributeOnClass()
    {
        $object = new class {
            #[Field(name: 'field_name')]
            private string $field;
        };

        $reader = new AttributesReader();

        $fields = $reader->getAttributes($object);
        $field = $fields[0];
        $this->assertEquals('field', $field['propertyName']);
        $this->assertEquals('field_name', $field['instance']->getName());
        $this->assertEquals('string', $field['instance']->getType());
    }

    public function testInvalidAttributes()
    {
        $this->expectException(InvalidAttibuteException::class);

        $object = new class {
            #[Field()]
            private string $field;
        };

        $reader = new AttributesReader();

        $fields = $reader->getAttributes($object);
    }
}
