<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
