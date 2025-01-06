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

namespace Parthenon\Billing\Subscriber;

use Parthenon\Billing\Customer\CustomerRegisterInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Event\PostUserSignupEvent;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CustomerCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CustomerRegisterInterface $customerRegister,
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

        $this->customerRegister->createCustomer($customer);
        $this->userRepository->save($customer);
    }
}
