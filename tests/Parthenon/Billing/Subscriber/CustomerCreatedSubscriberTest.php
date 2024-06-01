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

namespace Parthenon\Billing\Subscriber;

use BillaBear\Api\CustomersApi;
use BillaBear\Model\Customer;
use Parthenon\Billing\BillaBear\CustomerInterface;
use Parthenon\Billing\BillaBear\SdkFactory;
use Parthenon\Common\Address;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\User;
use Parthenon\User\Event\PostUserSignupEvent;
use Parthenon\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CustomerCreatedSubscriberTest extends TestCase
{
    public const CUSTOMER_ID = '393939';

    public function testRegisterUserCustomer()
    {
        $customerModel = new Customer();
        $customerModel->setId(self::CUSTOMER_ID);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $sdkFactory = $this->createMock(SdkFactory::class);
        $customersApi = $this->createMock(CustomersApi::class);
        $customersApi->expects($this->once())->method('createCustomer')->willReturn($customerModel);
        $sdkFactory->method('createCustomersApi')->willReturn($customersApi);

        $user = new class() extends User implements CustomerInterface {
            private $customerId;

            public function getDisplayName(): string
            {
                // TODO: Implement getDisplayName() method.
            }

            public function setBillingAddress(Address $address)
            {
                // TODO: Implement setBillingAddress() method.
            }

            public function getBillingAddress(): Address
            {
                $address = new Address();
                $address->setCountry('DE');

                return $address;
            }

            public function hasBillingAddress(): bool
            {
                return true;
            }

            public function setExternalCustomerReference($externalCustomerReference)
            {
                // TODO: Implement setExternalCustomerReference() method.
            }

            public function getExternalCustomerReference()
            {
                // TODO: Implement getExternalCustomerReference() method.
            }

            public function hasExternalCustomerReference(): bool
            {
                // TODO: Implement hasExternalCustomerReference() method.
            }

            public function getBillingEmail()
            {
                // TODO: Implement getBillingEmail() method.
            }

            public function setPaymentProviderDetailsUrl(?string $url)
            {
                // TODO: Implement setPaymentProviderDetailsUrl() method.
            }

            public function getPaymentProviderDetailsUrl()
            {
                // TODO: Implement getPaymentProviderDetailsUrl() method.
            }

            public function setCustomerId(string $customerId)
            {
                $this->customerId = $customerId;
            }

            public function getCustomerId(): string
            {
                return $this->customerId;
            }
        };
        $user->setEmail('iain@example.org');

        $event = new PostUserSignupEvent($user);

        $subject = new CustomerCreatedSubscriber($userRepository, $sdkFactory);
        $subject->userCreated($event);

        $this->assertEquals(self::CUSTOMER_ID, $user->getCustomerId());
    }

    public function testRegisterTeamCustomer()
    {
        $customerModel = new Customer();
        $customerModel->setId(self::CUSTOMER_ID);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $sdkFactory = $this->createMock(SdkFactory::class);
        $customersApi = $this->createMock(CustomersApi::class);
        $customersApi->expects($this->once())->method('createCustomer')->willReturn($customerModel);
        $sdkFactory->method('createCustomersApi')->willReturn($customersApi);

        $team = new class() extends Team implements CustomerInterface {
            private $customerId;

            public function getDisplayName(): string
            {
                // TODO: Implement getDisplayName() method.
            }

            public function setBillingAddress(Address $address)
            {
                // TODO: Implement setBillingAddress() method.
            }

            public function getBillingAddress(): Address
            {
                $address = new Address();
                $address->setCountry('DE');

                return $address;
            }

            public function hasBillingAddress(): bool
            {
                return true;
            }

            public function setExternalCustomerReference($externalCustomerReference)
            {
                // TODO: Implement setExternalCustomerReference() method.
            }

            public function getExternalCustomerReference()
            {
                // TODO: Implement getExternalCustomerReference() method.
            }

            public function hasExternalCustomerReference(): bool
            {
                // TODO: Implement hasExternalCustomerReference() method.
            }

            public function getBillingEmail()
            {
                // TODO: Implement getBillingEmail() method.
            }

            public function setPaymentProviderDetailsUrl(?string $url)
            {
                // TODO: Implement setPaymentProviderDetailsUrl() method.
            }

            public function getPaymentProviderDetailsUrl()
            {
                // TODO: Implement getPaymentProviderDetailsUrl() method.
            }

            public function setCustomerId(string $customerId)
            {
                $this->customerId = $customerId;
            }

            public function getCustomerId(): string
            {
                return $this->customerId;
            }

            public function getId()
            {
                // TODO: Implement getId() method.
            }
        };

        $user = new class() extends User implements MemberInterface {
            private TeamInterface $team;

            public function setTeam(TeamInterface $team): MemberInterface
            {
                $this->team = $team;

                return $this;
            }

            public function getTeam(): TeamInterface
            {
                return $this->team;
            }
        };
        $user->setEmail('iain@example.org');
        $user->setTeam($team);

        $event = new PostUserSignupEvent($user);

        $subject = new CustomerCreatedSubscriber($userRepository, $sdkFactory);
        $subject->userCreated($event);

        $this->assertEquals(self::CUSTOMER_ID, $team->getCustomerId());
    }
}
