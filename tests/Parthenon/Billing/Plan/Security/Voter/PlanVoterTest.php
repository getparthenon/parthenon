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
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Plan\CounterInterface;
use Parthenon\Billing\Plan\CounterManager;
use Parthenon\Billing\Plan\CustomerPlanInfoInterface;
use Parthenon\Billing\Plan\LimitableInterface;
use Parthenon\Billing\Plan\LimitedUserInterface;
use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PlanVoterTest extends TestCase
{
    public function testDoesNotHaveLimit()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $counter = $this->createMock(CounterInterface::class);
        $customerProvider = $this->createMock(CustomerProviderInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $customerPlanInfo = $this->createMock(CustomerPlanInfoInterface::class);
        $limitable = $this->createMock(LimitableInterface::class);

        $limitable->method('getLimitableName')->willReturn('limit');
        $counterManager->method('getCounter')->with($limitable)->willReturn($counter);
        $customerProvider->method('getCurrentCustomer')->willReturn($customer);
        $customerPlanInfo->method('getLimitCount')->with($customer, 'limit')->willReturn(0);
        $counter->method('getCount')->willReturn(1);

        $member = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $subject = new PlanVoter(
            $counterManager,
            $customerProvider,
            $customerPlanInfo,
        );

        $this->assertEquals(PlanVoter::ACCESS_DENIED, $subject->vote($token, $limitable, ['create']));
    }

    public function testDoesHaveLimit()
    {
        $token = $this->createMock(TokenInterface::class);
        $counterManager = $this->createMock(CounterManager::class);
        $counter = $this->createMock(CounterInterface::class);
        $customerProvider = $this->createMock(CustomerProviderInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $customerPlanInfo = $this->createMock(CustomerPlanInfoInterface::class);
        $limitable = $this->createMock(LimitableInterface::class);

        $limitable->method('getLimitableName')->willReturn('limit');
        $counterManager->method('getCounter')->with($limitable)->willReturn($counter);
        $customerProvider->method('getCurrentCustomer')->willReturn($customer);
        $customerPlanInfo->method('getLimitCount')->with($customer, 'limit')->willReturn(10);
        $counter->method('getCount')->willReturn(1);

        $member = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);

        $subject = new PlanVoter(
            $counterManager,
            $customerProvider,
            $customerPlanInfo,
        );

        $this->assertEquals(PlanVoter::ACCESS_GRANTED, $subject->vote($token, $limitable, ['create']));
    }
}
