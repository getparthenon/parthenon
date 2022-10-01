<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Listener;

use Doctrine\DBAL\Types\Type;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\User\Dbal\Types\UtcDateTimeType;
use Parthenon\User\Dbal\Types\UtcDateTimeTzType;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TimezoneAwareInterface;
use Parthenon\User\Team\CurrentTeamProviderInterface;
use Symfony\Component\Security\Core\Security;

class TimezoneListener
{
    use LoggerAwareTrait;

    public function __construct(
        private Security $security,
        private CurrentTeamProviderInterface $currentTeamProvider
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
