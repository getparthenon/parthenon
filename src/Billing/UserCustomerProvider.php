<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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
use Parthenon\Billing\Exception\NoCustomerException;
use Symfony\Bundle\SecurityBundle\Security;

final class UserCustomerProvider implements CustomerProviderInterface
{
    public function __construct(private Security $security)
    {
    }

    public function getCurrentCustomer(): CustomerInterface
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new NoCustomerException('No user found');
        }

        if (!$user instanceof CustomerInterface) {
            throw new NoCustomerException('User is not a customer');
        }

        return $user;
    }
}
