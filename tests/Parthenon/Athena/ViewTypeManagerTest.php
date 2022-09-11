<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Exception\InvalidViewTypeException;
use Parthenon\Athena\ViewType\TextViewType;
use PHPUnit\Framework\TestCase;

class ViewTypeManagerTest extends TestCase
{
    public function testReturnsTextTypeWhenOneExists()
    {
        $viewTypeManager = new ViewTypeManager();
        $viewTypeManager->add(new TextViewType());
        $this->assertInstanceOf(TextViewType::class, $viewTypeManager->get('text'));
    }

    public function testThrowExceptionsWhenNoneExists()
    {
        $this->expectException(InvalidViewTypeException::class);
        $viewTypeManager = new ViewTypeManager();
        $viewTypeManager->get('text');
    }
}
