<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Plan\Security\Voter;

use Parthenon\Billing\Exception\NoPlanFoundException;
use Parthenon\Billing\Plan\CounterManager;
use Parthenon\Billing\Plan\LimitedUserInterface;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PlanFeatureVoterTest extends TestCase
{
    public function testDeniesIfNotLoggedIn()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $feature = 'feature';

        $token->method('getUser')->willReturn(null);

        $voter = new PlanFeatureVoter($counterManager, $planManager);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $feature, ['feature_enabled']));
    }

    public function testAllowsIfNotLimitableUser()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $feature = 'feature';

        $member = new User();

        $token->method('getUser')->willReturn($member);

        $voter = new PlanFeatureVoter($counterManager, $planManager);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $feature, ['feature_enabled']));
    }

    public function testAllowsIfFeatureNotAString()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $feature = new \stdClass();

        $member = new User();

        $token->method('getUser')->willReturn($member);

        $voter = new PlanFeatureVoter($counterManager, $planManager);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $feature, ['feature_enabled']));
    }

    public function testAllowsIfNoPlan()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $feature = 'feature';

        $member = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willThrowException(new NoPlanFoundException());

        $voter = new PlanFeatureVoter($counterManager, $planManager);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $feature, ['feature_enabled']));
    }

    public function testRejectsIfPlanHasNoFeature()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $feature = 'feature';
        $plan = $this->createMock(Plan::class);
        $plan->method('hasFeature')->with($feature)->willReturn(false);

        $member = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willReturn($plan);

        $voter = new PlanFeatureVoter($counterManager, $planManager);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $feature, ['feature_enabled']));
    }

    public function testAllowsIfPlanHasFeature()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $feature = 'feature';
        $plan = $this->createMock(Plan::class);
        $plan->method('hasFeature')->with($feature)->willReturn(true);

        $member = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $planManager->method('getPlanForUser')->with($member)->willReturn($plan);

        $voter = new PlanFeatureVoter($counterManager, $planManager);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $feature, ['feature_enabled']));
    }
}
