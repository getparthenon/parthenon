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

use Parthenon\Billing\BillaBear\CustomerInterface;
use Parthenon\Billing\BillaBear\SdkFactory;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Common\Address;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Event\PostUserSignupEvent;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CustomerCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private SdkFactory $sdkFactory,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            PostUserSignupEvent::NAME => ['userCreated', 0],
        ];
    }

    public function userCreated(PostUserSignupEvent $event)
    {
        $user = $event->getUser();

        if ($user instanceof MemberInterface && $user->getTeam() instanceof CustomerInterface) {
            $customer = $user->getTeam();
        } elseif ($user instanceof CustomerInterface) {
            $customer = $user;
        } else {
            throw new NoCustomerException(sprintf('There is no BillaBear customer'));
        }

        $payload = $this->buildPayload($user, $customer);
        $response = $this->sdkFactory->createCustomersApi()->createCustomer($payload);
        $customer->setCustomerId($response->getId());

        $this->userRepository->save($customer);
    }

    private function buildPayload(UserInterface $user, CustomerInterface $internalCustomer): \BillaBear\Model\Customer
    {
        $customer = new \BillaBear\Model\Customer();
        $customer->setEmail($user->getEmail());
        if ($internalCustomer->hasBillingAddress()) {
            $customer->setAddress($this->convertBillingAddress($internalCustomer->getBillingAddress()));
        }

        return $customer;
    }

    private function convertBillingAddress(Address $address): \BillaBear\Model\Address
    {
        $apiAddress = new \BillaBear\Model\Address();
        $apiAddress->setStreetLineOne($address->getStreetLineOne());
        $apiAddress->setStreetLineTwo($address->getStreetLineTwo());
        $apiAddress->setCity($address->getCity());
        $apiAddress->setCountry($address->getCountry());
        $apiAddress->setPostcode($address->getPostcode());

        return $apiAddress;
    }
}
