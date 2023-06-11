<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Plan\Security\Voter;

use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Plan\Counter\TeamInviteCounterInterface;
use Parthenon\Billing\Plan\LimitedUserInterface;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
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
        private SubscriptionRepositoryInterface $subscriptionRepository,
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
        $subscriptions = $this->subscriptionRepository->getAllActiveForCustomer($customer);
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
