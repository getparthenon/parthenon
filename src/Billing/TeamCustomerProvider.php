<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Symfony\Component\Security\Core\Security;

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
