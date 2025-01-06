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
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TeamCustomerProvider implements CustomerProviderInterface
{
    public function __construct(private Security $security, private TeamRepositoryInterface $teamRepository)
    {
    }

    public function getCurrentCustomer(): CustomerInterface
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new NoCustomerException('Not a user');
        }

        if (!$user instanceof MemberInterface) {
            throw new NoCustomerException('Not a member of a team');
        }

        try {
            $team = $this->teamRepository->getByMember($user);
        } catch (NoEntityFoundException $exception) {
            throw new NoCustomerException('No team found', previous: $exception);
        }

        if (!$team instanceof CustomerInterface) {
            throw new NoCustomerException('Team not a customer');
        }

        return $team;
    }
}
