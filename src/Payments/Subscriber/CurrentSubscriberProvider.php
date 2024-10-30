<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Payments\Subscriber;

use Parthenon\Payments\Exception\InvalidSubscriberException;
use Parthenon\User\Team\CurrentTeamProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class CurrentSubscriberProvider implements CurrentSubscriberProviderInterface
{
    public function __construct(private ?string $type, private Security $security, private CurrentTeamProviderInterface $currentTeamProvider)
    {
    }

    public function getSubscriber(): SubscriberInterface
    {
        if (SubscriberInterface::TYPE_USER === $this->type) {
            return $this->getUserSubscriber();
        }

        if (SubscriberInterface::TYPE_TEAM === $this->type) {
            return $this->getTeamSubscriber();
        }

        throw new InvalidSubscriberException(sprintf("'%s'  is not a valid subscriber type", $this->type));
    }

    /**
     * @throws InvalidSubscriberException
     */
    private function getTeamSubscriber(): SubscriberInterface
    {
        $team = $this->currentTeamProvider->getCurrentTeam();

        if (!$team instanceof SubscriberInterface) {
            throw new InvalidSubscriberException('User does not implement the SubscriberInterface');
        }

        return $team;
    }

    /**
     * @throws InvalidSubscriberException
     */
    private function getUserSubscriber(): SubscriberInterface
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new InvalidSubscriberException('User not logged in');
        }

        if (!$user instanceof SubscriberInterface) {
            throw new InvalidSubscriberException('User does not implement the SubscriberInterface');
        }

        return $user;
    }
}
