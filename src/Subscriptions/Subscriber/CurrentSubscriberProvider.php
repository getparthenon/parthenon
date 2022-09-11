<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Subscriber;

use Parthenon\Subscriptions\Exception\InvalidSubscriberException;
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

        throw new InvalidSubscriberException(sprintf("'%s'  is not a valid type", $this->type));
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
