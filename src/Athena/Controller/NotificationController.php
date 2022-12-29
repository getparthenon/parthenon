<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Controller;

use Parthenon\Athena\Repository\NotificationRepositoryInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NotificationController
{
    #[Template('@Parthenon/athena/notification/list.html.twig')]
    public function viewAll(LoggerInterface $logger, NotificationRepositoryInterface $notificationRepository)
    {
        $logger->info('Notification list viewed');

        return ['notifications' => $notificationRepository->getList()];
    }

    public function markAsRead(Request $request, LoggerInterface $logger, NotificationRepositoryInterface $notificationRepository)
    {
        $logger->info('Notification marked as read', ['notification_id' => (string) $request->get('id')]);

        $notification = $notificationRepository->getById($request->get('id'));
        $notification->markAsRead();
        $notificationRepository->save($notification);

        return new JsonResponse(['read' => true]);
    }
}
