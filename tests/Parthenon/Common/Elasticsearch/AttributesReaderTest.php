<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
