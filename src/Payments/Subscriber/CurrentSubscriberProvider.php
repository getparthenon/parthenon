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

namespace Parthenon\Payments\Subscriber;

use Parthenon\Payments\Exception\InvalidSubscriberException;
use Parthenon\User\Team\CurrentTeamProviderInterface;
use Symfony\Component\Security\Core\Security;

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

        throw new InvalidSubscriberException(sprintf("'%s' is not a subscriber valid type", $this->type));
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
