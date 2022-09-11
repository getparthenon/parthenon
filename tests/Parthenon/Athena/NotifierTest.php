<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
