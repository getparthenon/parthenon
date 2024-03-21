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

namespace Parthenon\Billing\Webhook;

use Obol\Model\Events\EventInterface;
use PHPUnit\Framework\TestCase;

class HandlerManagerTest extends TestCase
{
    public function testCallsHandler()
    {
        $event = $this->createMock(EventInterface::class);

        $handlerOne = $this->createMock(HandlerInterface::class);
        $handlerOne->method('supports')->with($event)->willReturn(false);
        $handlerOne->expects($this->never())->method('handle')->with($event);

        $handlerTwo = $this->createMock(HandlerInterface::class);
        $handlerTwo->method('supports')->with($event)->willReturn(true);
        $handlerTwo->expects($this->once())->method('handle')->with($event);

        $manager = new HandlerManager();
        $manager->addHandler($handlerOne);
        $manager->addHandler($handlerTwo);
        $manager->handle($event);
    }
}
