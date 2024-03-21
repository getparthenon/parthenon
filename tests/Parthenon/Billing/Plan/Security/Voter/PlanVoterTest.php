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
