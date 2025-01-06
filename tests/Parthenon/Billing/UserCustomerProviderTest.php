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

namespace Parthenon\Billing;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\EmbeddedSubscription;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Common\Address;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
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
        $security->method('getUser')->willReturn(new class implements UserInterface {
            public function getRoles(): array
            {
                // TODO: Implement getRoles() method.
            }

            public function eraseCredentials(): void
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
        $security->method('getUser')->willReturn(new class implements UserInterface, CustomerInterface {
            public function getRoles(): array
            {
                // TODO: Implement getRoles() method.
            }

            public function eraseCredentials(): void
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

            public function setEnabled(bool $enabled)
            {
                // TODO: Implement setEnabled() method.
            }

            public function isEnabled(): bool
            {
                // TODO: Implement isEnabled() method.
            }
        });
        $userCustomerProvider = new UserCustomerProvider($security);
        $actual = $userCustomerProvider->getCurrentCustomer();

        $this->assertInstanceOf(CustomerInterface::class, $actual);
    }
}
