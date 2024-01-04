<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\EmbeddedSubscription;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Common\Address;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserCustomerProviderTest extends TestCase
{
    public function testNoUser()
    {
        $this->expectException(NoCustomerException::class);

        $security = $this->createMock(Security::class);

        $userCustomerProvider = new UserCustomerProvider($security);
        $userCustomerProvider->getCurrentCustomer();
    }

    public function testUserIsNotCustomer()
    {
        $this->expectException(NoCustomerException::class);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(new class() implements UserInterface {
            public function getRoles(): array
            {
                // TODO: Implement getRoles() method.
            }

            public function eraseCredentials()
            {
                // TODO: Implement eraseCredentials() method.
            }

            public function getUserIdentifier(): string
            {
                // TODO: Implement getUserIdentifier() method.
            }
        });
        $userCustomerProvider = new UserCustomerProvider($security);
        $userCustomerProvider->getCurrentCustomer();
    }

    public function testUserIsCustomer()
    {
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(new class() implements UserInterface, CustomerInterface {
            public function getRoles(): array
            {
                // TODO: Implement getRoles() method.
            }

            public function eraseCredentials()
            {
                // TODO: Implement eraseCredentials() method.
            }

            public function getUserIdentifier(): string
            {
                // TODO: Implement getUserIdentifier() method.
            }

            public function hasSubscription(): bool
            {
                // TODO: Implement hasSubscription() method.
            }

            public function getSubscription(): EmbeddedSubscription
            {
                // TODO: Implement getSubscription() method.
            }

            public function setSubscription(EmbeddedSubscription $subscription)
            {
                // TODO: Implement setSubscription() method.
            }

            public function setBillingAddress(Address $address)
            {
                // TODO: Implement setBillingAddress() method.
            }

            public function getBillingAddress(): Address
            {
                // TODO: Implement getBillingAddress() method.
            }

            public function hasBillingAddress(): bool
            {
                // TODO: Implement hasBillingAddress() method.
            }

            public function hasActiveSubscription(): bool
            {
                // TODO: Implement hasActiveSubscription() method.
            }

            public function setExternalCustomerReference($customerReference)
            {
                // TODO: Implement setExternalCustomerReference() method.
            }

            public function getExternalCustomerReference()
            {
                // TODO: Implement getExternalCustomerReference() method.
            }

            public function getBillingEmail()
            {
                // TODO: Implement getBillingEmail() method.
            }

            public function getId()
            {
                // TODO: Implement getId() method.
            }

            public function getDisplayName(): string
            {
                // TODO: Implement getDisplayName() method.
            }

            public function getPaymentProviderDetailsUrl()
            {
                // TODO: Implement getPaymentProviderDetailsUrl() method.
            }

            public function hasExternalCustomerReference(): bool
            {
                return false;
            }

            public function setPaymentProviderDetailsUrl(?string $url)
            {
                // TODO: Implement setPaymentProviderDetailsUrl() method.
            }
        });
        $userCustomerProvider = new UserCustomerProvider($security);
        $actual = $userCustomerProvider->getCurrentCustomer();

        $this->assertInstanceOf(CustomerInterface::class, $actual);
    }
}
