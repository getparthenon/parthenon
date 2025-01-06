<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\Payments\Plan\Security\Voter;

use Parthenon\Payments\Entity\Subscription;
use Parthenon\Payments\Plan\Counter\TeamInviteCounterInterface;
use Parthenon\Payments\Plan\LimitedUserInterface;
use Parthenon\Payments\Plan\Plan;
use Parthenon\Payments\Plan\PlanManagerInterface;
use Parthenon\Payments\Subscriber\CurrentSubscriberProvider;
use Parthenon\Payments\Subscriber\SubscriberInterface;
use Parthenon\User\Entity\TeamInviteCode;
use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class InviteVoterTest extends TestCase
{
    public function testDeniesIfNotLoggedIn()
    {
        $token = $this->createMock(TokenInterface::class);
        $counter = $this->createMock(TeamInviteCounterInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $teamInviteCode = new TeamInviteCode();
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $token->method('getUser')->willReturn(null);

        $voter = new InviteVoter($counter, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $teamInviteCode, ['create']));
    }

    public function testDeniesIfOverLimitFromUserCount()
    {
        $token = $this->createMock(TokenInterface::class);
        $counter = $this->createMock(TeamInviteCounterInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = new TeamInviteCode();
        $plan = $this->createMock(Plan::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $subscriber = new class implements SubscriberInterface {
            public function setSubscription(Subscription $subscription)
            {
                // TODO: Implement setSubscription() method.
            }

            public function getSubscription(): ?Subscription
            {
                // TODO: Implement getSubscription() method.
            }

            public function hasActiveSubscription(): bool
            {
                return true;
            }

            public function getIdentifier(): string
            {
                // TODO: Implement getIdentifier() method.
            }
        };
        $currentSubscriberProvider->method('getSubscriber')->willReturn($subscriber);

        $member = new class extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willReturn($plan);
        $plan->method('isPerSeat')->with($limitable)->willReturn(false);
        $plan->method('getUserCount')->with($limitable)->willReturn(1);
        $counter->method('getCount')->with($member)->willReturn(4);

        $voter = new InviteVoter($counter, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $limitable, ['create']));
    }

    public function testDeniesIfOverLimitFromPerSeat()
    {
        $token = $this->createMock(TokenInterface::class);
        $counter = $this->createMock(TeamInviteCounterInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = new TeamInviteCode();
        $plan = $this->createMock(Plan::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $subscriber = new class implements SubscriberInterface {
            public function setSubscription(Subscription $subscription)
            {
                // TODO: Implement setSubscription() method.
            }

            public function getSubscription(): ?Subscription
            {
                $subscription = new Subscription();
                $subscription->setSeats(1);

                return $subscription;
            }

            public function hasActiveSubscription(): bool
            {
                return true;
            }

            public function getIdentifier(): string
            {
                // TODO: Implement getIdentifier() method.
            }
        };
        $currentSubscriberProvider->method('getSubscriber')->willReturn($subscriber);

        $member = new class extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willReturn($plan);
        $plan->method('isPerSeat')->with($limitable)->willReturn(true);
        $plan->method('getUserCount')->with($limitable)->willReturn(10);
        $counter->method('getCount')->with($member)->willReturn(4);

        $voter = new InviteVoter($counter, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $limitable, ['create']));
    }

    public function testAllowsFromPerSeat()
    {
        $token = $this->createMock(TokenInterface::class);
        $counter = $this->createMock(TeamInviteCounterInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = new TeamInviteCode();
        $plan = $this->createMock(Plan::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $subscriber = new class implements SubscriberInterface {
            public function setSubscription(Subscription $subscription)
            {
                // TODO: Implement setSubscription() method.
            }

            public function getSubscription(): ?Subscription
            {
                $subscription = new Subscription();
                $subscription->setSeats(10);

                return $subscription;
            }

            public function hasActiveSubscription(): bool
            {
                return true;
            }

            public function getIdentifier(): string
            {
                // TODO: Implement getIdentifier() method.
            }
        };
        $currentSubscriberProvider->method('getSubscriber')->willReturn($subscriber);

        $member = new class extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willReturn($plan);
        $plan->method('isPerSeat')->with($limitable)->willReturn(true);
        $plan->method('getUserCount')->with($limitable)->willReturn(1);
        $counter->method('getCount')->with($member)->willReturn(4);

        $voter = new InviteVoter($counter, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $limitable, ['create']));
    }

    public function testAllowsFromUserCount()
    {
        $token = $this->createMock(TokenInterface::class);
        $counter = $this->createMock(TeamInviteCounterInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = new TeamInviteCode();
        $plan = $this->createMock(Plan::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $subscriber = new class implements SubscriberInterface {
            public function setSubscription(Subscription $subscription)
            {
                // TODO: Implement setSubscription() method.
            }

            public function getSubscription(): ?Subscription
            {
                $subscription = new Subscription();
                $subscription->setSeats(1);

                return $subscription;
            }

            public function hasActiveSubscription(): bool
            {
                return true;
            }

            public function getIdentifier(): string
            {
                // TODO: Implement getIdentifier() method.
            }
        };
        $currentSubscriberProvider->method('getSubscriber')->willReturn($subscriber);

        $member = new class extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willReturn($plan);
        $plan->method('isPerSeat')->with($limitable)->willReturn(false);
        $plan->method('getUserCount')->with($limitable)->willReturn(10);
        $counter->method('getCount')->with($member)->willReturn(4);

        $voter = new InviteVoter($counter, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $limitable, ['create']));
    }
}
