<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Repository;

use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Subscriptions\Subscriber\SubscriberInterface;
use Parthenon\Subscriptions\SubscriptionInterface;
use Parthenon\User\Entity\UserInterface;

interface SubscriberRepositoryInterface extends CrudRepositoryInterface
{
    public function getSubscriptionForUser(UserInterface $user): SubscriptionInterface;

    /**
     * @return SubscriberInterface[]
     */
    public function findAllSubscriptions(): array;
}
