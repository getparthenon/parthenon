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

namespace Parthenon\Common\Elasticsearch;

use Parthenon\Common\Elasticsearch\Attributes\Field;
use Parthenon\Common\Exception\Elasticsearch\InvalidAttibuteException;
use PHPUnit\Framework\TestCase;

class AttributesReaderTest extends TestCase
{
    public function testFindsAttributeOnClass()
    {
        $object = new class() {
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

        $object = new class() {
            #[Field()]
            private string $field;
        };

        $reader = new AttributesReader();

        $fields = $reader->getAttributes($object);
    }
}
