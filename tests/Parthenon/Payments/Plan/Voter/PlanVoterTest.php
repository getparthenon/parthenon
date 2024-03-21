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

namespace Parthenon\Payments\Plan\Security\Voter;

use Parthenon\Payments\Entity\Subscription;
use Parthenon\Payments\Exception\NoCounterException;
use Parthenon\Payments\Exception\NoPlanFoundException;
use Parthenon\Payments\Plan\CounterInterface;
use Parthenon\Payments\Plan\CounterManager;
use Parthenon\Payments\Plan\LimitableInterface;
use Parthenon\Payments\Plan\LimitedUserInterface;
use Parthenon\Payments\Plan\Plan;
use Parthenon\Payments\Plan\PlanManagerInterface;
use Parthenon\Payments\Subscriber\CurrentSubscriberProvider;
use Parthenon\Payments\Subscriber\SubscriberInterface;
use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PlanVoterTest extends TestCase
{
    public function testDeniesIfNotLoggedIn()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = $this->createMock(LimitableInterface::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $token->method('getUser')->willReturn(null);

        $voter = new PlanVoter($counterManager, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $limitable, ['create']));
    }

    public function testAllowsIfNotLimitableUser()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = $this->createMock(LimitableInterface::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $member = new User();

        $token->method('getUser')->willReturn($member);

        $voter = new PlanVoter($counterManager, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $limitable, ['create']));
    }

    public function testDeniesIfSubscriptionNotActive()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = $this->createMock(LimitableInterface::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);
        $subscriber = new class() implements SubscriberInterface {
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
                return false;
            }

            public function getIdentifier(): string
            {
                // TODO: Implement getIdentifier() method.
            }
        };
        $currentSubscriberProvider->method('getSubscriber')->willReturn($subscriber);

        $member = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $voter = new PlanVoter($counterManager, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $limitable, ['create']));
    }

    public function testDeniesIfOverLimit()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = $this->createMock(LimitableInterface::class);
        $plan = $this->createMock(Plan::class);
        $counter = $this->createMock(CounterInterface::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $subscriber = new class() implements SubscriberInterface {
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

        $member = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willReturn($plan);
        $counterManager->method('getCounter')->with($limitable)->willReturn($counter);
        $plan->method('getLimit')->with($limitable)->willReturn(1);
        $counter->method('getCount')->with($member, $limitable)->willReturn(4);

        $voter = new PlanVoter($counterManager, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $limitable, ['create']));
    }

    public function testAllowsIfNoLimit()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = $this->createMock(LimitableInterface::class);
        $plan = $this->createMock(Plan::class);
        $counter = $this->createMock(CounterInterface::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $subscriber = new class() implements SubscriberInterface {
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

        $member = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willReturn($plan);
        $counterManager->method('getCounter')->with($limitable)->willReturn($counter);
        $plan->method('getLimit')->with($limitable)->willReturn(-1);
        $counter->method('getCount')->with($member, $limitable)->willReturn(4);

        $voter = new PlanVoter($counterManager, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $limitable, ['create']));
    }

    public function testAllowsIfNoPlan()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = $this->createMock(LimitableInterface::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $subscriber = new class() implements SubscriberInterface {
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

        $member = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willThrowException(new NoPlanFoundException());

        $voter = new PlanVoter($counterManager, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $limitable, ['create']));
    }

    public function testAllowsIfNoCounter()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $limitable = $this->createMock(LimitableInterface::class);
        $plan = $this->createMock(Plan::class);
        $currentSubscriberProvider = $this->createMock(CurrentSubscriberProvider::class);

        $subscriber = new class() implements SubscriberInterface {
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

        $member = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willReturn($plan);
        $counterManager->method('getCounter')->with($limitable)->willThrowException(new NoCounterException());
        $plan->method('getLimit')->with($limitable)->willReturn(115);

        $voter = new PlanVoter($counterManager, $planManager, $currentSubscriberProvider);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $limitable, ['create']));
    }
}
