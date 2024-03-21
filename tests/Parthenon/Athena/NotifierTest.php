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

use Parthenon\Athena\Entity\Link;
use Parthenon\Athena\Entity\Notification;
use Parthenon\Athena\Repository\NotificationRepositoryInterface;
use PHPUnit\Framework\TestCase;

class NotifierTest extends TestCase
{
    public function testItCallsRepository()
    {
        $notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $notificationRepository->expects($this->once())->method('save')->with($this->isInstanceOf(Notification::class));

        $notifier = new Notifier($notificationRepository);
        $notifier->notify('A message', new Link('url'));
    }
}
