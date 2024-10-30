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

namespace Parthenon\Billing\Plan\Security\Voter;

use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Plan\Counter\TeamInviteCounterInterface;
use Parthenon\Billing\Plan\LimitedUserInterface;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Subscription\SubscriptionProviderInterface;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInviteCode;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class InviteVoter extends Voter
{
    public function __construct(
        private TeamInviteCounterInterface $teamInviteCounter,
        private PlanManagerInterface $planManager,
        private CustomerProviderInterface $customerProvider,
        private SubscriptionProviderInterface $subscriptionRepository,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof TeamInviteCode;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof MemberInterface) {
            return false;
        }
        if (!$user instanceof LimitedUserInterface) {
            return false;
        }

        $customer = $this->customerProvider->getCurrentCustomer();
        $subscriptions = $this->subscriptionRepository->getSubscriptionsForCustomer($customer);
        $inviteLimit = 0;

        foreach ($subscriptions as $subscription) {
            $plan = $this->planManager->getPlanByName($subscription->getPlanName());
            if ($plan->isPerSeat()) {
                $subscriber = $this->customerProvider->getCurrentCustomer();
                if (!$subscriber->hasActiveSubscription()) {
                    return false;
                }
                $inviteLimit += $subscriber->getSubscription()->getSeats();
            } else {
                $inviteLimit += $plan->getUserCount();
            }
        }

        $inviteCount = $this->teamInviteCounter->getCount($user);

        return $inviteCount < $inviteLimit;
    }
}
