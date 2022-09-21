<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Subscriptions\Plan\Security\Voter;

use Parthenon\Subscriptions\Entity\Subscription;
use Parthenon\Subscriptions\Exception\NoCounterException;
use Parthenon\Subscriptions\Exception\NoPlanFoundException;
use Parthenon\Subscriptions\Plan\CounterInterface;
use Parthenon\Subscriptions\Plan\CounterManager;
use Parthenon\Subscriptions\Plan\LimitableInterface;
use Parthenon\Subscriptions\Plan\LimitedUserInterface;
use Parthenon\Subscriptions\Plan\Plan;
use Parthenon\Subscriptions\Plan\PlanManagerInterface;
use Parthenon\Subscriptions\Subscriber\CurrentSubscriberProvider;
use Parthenon\Subscriptions\Subscriber\SubscriberInterface;
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
