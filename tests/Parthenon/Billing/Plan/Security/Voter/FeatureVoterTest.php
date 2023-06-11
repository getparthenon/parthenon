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

        $member = new class() extends User implements LimitedUserInterface {
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

        $member = new class() extends User implements LimitedUserInterface {
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
