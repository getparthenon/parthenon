<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Repository;

use Parthenon\Athena\Repository\OdmCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\User;

class UserOdmRepository extends OdmCrudRepository implements UserRepositoryInterface
{
    public function findByEmail($user): User
    {
        $user = $this->documentRepository->findOneBy(['email' => $user]);

        if (!$user instanceof User) {
            throw new NoEntityFoundException();
        }

        return $user;
    }

    public function findByConfirmationCode(string $confirmationCode): User
    {
        $user = $this->documentRepository->findOneBy(['confirmationCode' => $confirmationCode]);

        if (!$user instanceof User) {
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

        $twentyFourHoursCount = $this->documentRepository->createQueryBuilder()->field('createdAt')->gt($twentyFourHours)->getQuery()->execute()->getMatchedCount();
        $previousHoursCount = $this->documentRepository->createQueryBuilder()->field('createdAt')->gt($fourtyEightHours)->field('createdAt')->lt($oneWeek)->getQuery()->execute()->getMatchedCount();

        $lastWeek = $this->documentRepository->createQueryBuilder()->field('createdAt')->gt($oneWeek)->getQuery()->execute()->getMatchedCount();
        $previousWeek = $this->documentRepository->createQueryBuilder()->field('createdAt')->gt($twoWeeks)->field('createdAt')->lt($twoWeeks)->getQuery()->execute()->getMatchedCount();

        return [
            'twenty_four_hour_count' => $twentyFourHoursCount,
            'previous_twenty_four_hour_count' => $previousHoursCount,
            'this_week_count' => $lastWeek,
            'last_week_count' => $previousWeek,
        ];
    }
}
