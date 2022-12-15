<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Plan\Security\Voter;

use Parthenon\Payments\Plan\Counter\TeamInviteCounterInterface;
use Parthenon\Payments\Plan\LimitedUserInterface;
use Parthenon\Payments\Plan\PlanManagerInterface;
use Parthenon\Payments\Subscriber\CurrentSubscriberProvider;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInviteCode;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class InviteVoter extends Voter
{
    public function __construct(
        private TeamInviteCounterInterface $teamInviteCounter,
        private PlanManagerInterface $planManager,
        private CurrentSubscriberProvider $currentSubscriberProvider
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

        $plan = $this->planManager->getPlanForUser($user);

        if ($plan->isPerSeat()) {
            $subscriber = $this->currentSubscriberProvider->getSubscriber();
            $inviteLimit = $subscriber->getSubscription()->getSeats();
        } else {
            $inviteLimit = $plan->getUserCount();
        }

        $inviteCount = $this->teamInviteCounter->getCount($user);

        return $inviteCount < $inviteLimit;
    }
}
