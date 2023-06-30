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
