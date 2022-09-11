<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Entity\Link;
use Parthenon\Athena\Entity\Notification;
use Parthenon\Athena\Repository\NotificationRepositoryInterface;

final class Notifier implements NotifierInterface
{
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function notify(string $message, Link $link): void
    {
        $notification = new Notification();
        $notification->setCreatedAt(new \DateTime('now'));
        $notification->setMessageTemplate($message);
        $notification->setLink($link);

        $this->notificationRepository->save($notification);
    }
}
