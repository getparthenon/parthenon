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

namespace Parthenon\Athena\Controller;

use Parthenon\Athena\Repository\NotificationRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
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
