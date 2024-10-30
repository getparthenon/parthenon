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

namespace Parthenon\User\Listener;

use Doctrine\DBAL\Types\Type;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\User\Dbal\Types\UtcDateTimeType;
use Parthenon\User\Dbal\Types\UtcDateTimeTzType;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TimezoneAwareInterface;
use Parthenon\User\Team\CurrentTeamProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TimezoneListener
{
    use LoggerAwareTrait;

    public function __construct(
        private Security $security,
        private CurrentTeamProviderInterface $currentTeamProvider,
    ) {
    }

    public function onKernelRequest()
    {
        $user = $this->security->getUser();

        if (null === $user) {
            return;
        }

        if ($user instanceof TimezoneAwareInterface) {
            $this->getLogger()->info('Setting timezone to user setting', ['timezone' => $user->getTimezone()->getName()]);
            date_default_timezone_set('Europe/Berlin');
            UtcDateTimeTzType::setTimeZone($user->getTimezone()->getName());
            UtcDateTimeType::setTimeZone($user->getTimezone()->getName());
            Type::overrideType('datetime', UtcDateTimeType::class);
            Type::overrideType('datetimetz', UtcDateTimeTzType::class);

            return;
        }

        if (!$user instanceof MemberInterface) {
            return;
        }

        $team = $this->currentTeamProvider->getCurrentTeam();
        if ($team instanceof TimezoneAwareInterface) {
            $this->getLogger()->info('Setting timezone to team setting', ['timezone' => $team->getTimezone()->getName()]);
            date_default_timezone_set($team->getTimezone()->getName());
            UtcDateTimeTzType::setTimeZone($team->getTimezone()->getName());
            UtcDateTimeType::setTimeZone($team->getTimezone()->getName());
            Type::overrideType('datetime', UtcDateTimeType::class);
            Type::overrideType('datetimetz', UtcDateTimeTzType::class);

            return;
        }
    }
}
