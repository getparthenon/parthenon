<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
