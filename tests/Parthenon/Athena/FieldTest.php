<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\ViewType\TextViewType;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    public function testReturnsHeaderName()
    {
        $field = new Field('first_name', new TextViewType());
        $this->assertEquals('First Name', $field->getHeaderName());
    }
}
