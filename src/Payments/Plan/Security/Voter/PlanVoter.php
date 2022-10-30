<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Plan\Security\Voter;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Payments\Exception\NoCounterException;
use Parthenon\Payments\Exception\NoPlanFoundException;
use Parthenon\Payments\Plan\CounterManager;
use Parthenon\Payments\Plan\LimitableInterface;
use Parthenon\Payments\Plan\LimitedUserInterface;
use Parthenon\Payments\Plan\PlanManagerInterface;
use Parthenon\Payments\Subscriber\CurrentSubscriberProvider;
use Parthenon\User\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class PlanVoter extends Voter
{
    use LoggerAwareTrait;

    public const SUPPORTED_ATTRIBUTES = ['create', 'enable', 'enabled'];

    public function __construct(private CounterManager $counterManager, private PlanManagerInterface $planManager, private CurrentSubscriberProvider $currentSubscriberProvider)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, self::SUPPORTED_ATTRIBUTES)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$subject instanceof LimitableInterface) {
            return true;
        }

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof LimitedUserInterface) {
            return true;
        }

        $subscriber = $this->currentSubscriberProvider->getSubscriber();

        if (!$subscriber->hasActiveSubscription()) {
            return false;
        }

        try {
            $plan = $this->planManager->getPlanForUser($user);
        } catch (NoPlanFoundException $exception) {
            $this->getLogger()->warning('No plan for user', ['plan_name' => $user->getPlanName()]);

            return true;
        }

        $limit = $plan->getLimit($subject);

        if (-1 === $limit) {
            return true;
        }

        try {
            $counter = $this->counterManager->getCounter($subject);
        } catch (NoCounterException $e) {
            $this->getLogger()->warning('No counter for limitable object found', ['limitable_class' => get_class($subject)]);

            return true;
        }

        return $counter->getCount($user, $subject) < $limit;
    }
}
