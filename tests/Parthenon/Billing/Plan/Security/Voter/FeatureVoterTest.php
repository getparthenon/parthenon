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

namespace Parthenon\Billing\Plan\Security\Voter;

use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Plan\CustomerPlanInfoInterface;
use Parthenon\Billing\Plan\LimitedUserInterface;
use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PlanFeatureVoterTest extends TestCase
{
    public function testHasFeature()
    {
        $feature = 'feature';
        $token = $this->createMock(TokenInterface::class);
        $customerProvider = $this->createMock(CustomerProviderInterface::class);
        $customerPlanInfo = $this->createMock(CustomerPlanInfoInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $member = new class extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);
        $customerProvider->method('getCurrentCustomer')->willReturn($customer);
        $customerPlanInfo->method('hasFeature')->with($customer, $feature)->willReturn(false);

        $voter = new PlanFeatureVoter($customerProvider, $customerPlanInfo);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $feature, ['feature_enabled']));
    }

    public function testDoesNotHaveFeature()
    {
        $feature = 'feature';
        $token = $this->createMock(TokenInterface::class);
        $customerProvider = $this->createMock(CustomerProviderInterface::class);
        $customerPlanInfo = $this->createMock(CustomerPlanInfoInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $member = new class extends User implements LimitedUserInterface {
            public function getPlanName(): string
            {
                return 'plan';
            }
        };

        $token->method('getUser')->willReturn($member);
        $customerProvider->method('getCurrentCustomer')->willReturn($customer);
        $customerPlanInfo->method('hasFeature')->with($customer, $feature)->willReturn(true);

        $voter = new PlanFeatureVoter($customerProvider, $customerPlanInfo);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $feature, ['feature_enabled']));
    }
}
