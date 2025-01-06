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

namespace Parthenon\User\Repository;

use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\User;

class UserRepository extends DoctrineCrudRepository implements UserRepositoryInterface, ActiveMembersRepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function findByEmail($username): User
    {
        $user = $this->entityRepository->findOneBy(['email' => $username, 'isDeleted' => false]);

        if (!$user || $user->isDeleted()) {
            throw new NoEntityFoundException();
        }

        return $user;
    }

    public function findByConfirmationCode(string $confirmationCode): User
    {
        $user = $this->entityRepository->findOneBy(['confirmationCode' => $confirmationCode]);

        if (!$user || $user->isDeleted()) {
            throw new NoEntityFoundException();
        }

        return $user;
    }

    public function getUserSignupStats(): array
    {
        $twentyFourHours = new \DateTime('-24 hours');
        $fourtyEightHours = new \DateTime('-48 hours');

        $oneWeek = new \DateTime('-1 week');
        $twoWeeks = new \DateTime('-2 weeks');

        $todayCount = $this->entityRepository->createQueryBuilder('u')
            ->select('COUNT(u.id) as user_count')
            ->where('u.createdAt >= :twentyFourHours')
            ->setParameter(':twentyFourHours', $twentyFourHours)->getQuery()
            ->getSingleResult();
        $yesterdayCount = $this->entityRepository->createQueryBuilder('u')
            ->select('COUNT(u.id) as user_count')
            ->where('u.createdAt < :twentyFourHours')
            ->andWhere('u.createdAt >= :fourtyEightHours')
            ->setParameter(':twentyFourHours', $twentyFourHours)
            ->setParameter(':fourtyEightHours', $fourtyEightHours)
            ->getQuery()
            ->getSingleResult();

        $weekCount = $this->entityRepository->createQueryBuilder('u')
            ->select('COUNT(u.id) as user_count')
            ->where('u.createdAt >= :oneWeek')
            ->setParameter(':oneWeek', $oneWeek)
            ->getQuery()
            ->getSingleResult();

        $lastWeekCount = $this->entityRepository->createQueryBuilder('u')
            ->select('COUNT(u.id) as user_count')
            ->where('u.createdAt < :oneWeek')
            ->andWhere('u.createdAt >= :twoWeeks')
            ->setParameter(':oneWeek', $oneWeek)
            ->setParameter(':twoWeeks', $twoWeeks)
            ->getQuery()
            ->getSingleResult();

        return [
            'twenty_four_hour_count' => current($todayCount),
            'previous_twenty_four_hour_count' => current($yesterdayCount),
            'this_week_count' => count($weekCount),
            'last_week_count' => count($lastWeekCount),
        ];
    }

    public function getEntity()
    {
        $className = $this->entityRepository->getClassName();

        return new $className();
    }

    public function getCountForActiveTeamMemebers(TeamInterface $team): int
    {
        return $this->entityRepository->count(['team' => $team, 'isDeleted' => false]);
    }

    public function getMembers(TeamInterface $team): array
    {
        return $this->entityRepository->findBy(['team' => $team]);
    }
}
