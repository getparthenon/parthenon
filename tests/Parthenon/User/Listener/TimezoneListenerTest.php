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

namespace Partheon\User\Listener;

use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\TimezoneAwareInterface;
use Parthenon\User\Entity\User;
use Parthenon\User\Listener\TimezoneListener;
use Parthenon\User\Team\CurrentTeamProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class TimezoneListenerTest extends TestCase
{
    public function testNoUser()
    {
        $security = $this->createMock(Security::class);
        $teamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $security->method('getUser')->willReturn(null);

        date_default_timezone_set('UTC');

        $timezoneListener = new TimezoneListener($security, $teamProvider);
        $timezoneListener->onKernelRequest();

        $this->assertEquals('UTC', date_default_timezone_get());
    }

    public function testUserIsNotTimezoneAware()
    {
        $security = $this->createMock(Security::class);
        $teamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new User();
        $security->method('getUser')->willReturn($user);

        date_default_timezone_set('UTC');

        $timezoneListener = new TimezoneListener($security, $teamProvider);
        $timezoneListener->onKernelRequest();

        $this->assertEquals('UTC', date_default_timezone_get());
    }

    public function testUserIsTimezoneAware()
    {
        $security = $this->createMock(Security::class);
        $teamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new class extends User implements TimezoneAwareInterface {
            public function getTimezone(): \DateTimeZone
            {
                return new \DateTimeZone('Europe/Berlin');
            }

            public function hasTimezone(): bool
            {
                return true;
            }
        };
        $security->method('getUser')->willReturn($user);

        date_default_timezone_set('UTC');

        $timezoneListener = new TimezoneListener($security, $teamProvider);
        $timezoneListener->onKernelRequest();

        $this->assertEquals('Europe/Berlin', date_default_timezone_get());
    }

    public function testTeamIsNotTimezoneAware()
    {
        $security = $this->createMock(Security::class);
        $teamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new User();
        $security->method('getUser')->willReturn($user);

        $team = new Team();
        $teamProvider->method('getCurrentTeam')->willReturn($team);

        date_default_timezone_set('UTC');

        $timezoneListener = new TimezoneListener($security, $teamProvider);
        $timezoneListener->onKernelRequest();

        $this->assertEquals('UTC', date_default_timezone_get());
    }

    public function testTeamIsTimezoneAware()
    {
        $security = $this->createMock(Security::class);
        $teamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new class extends User implements MemberInterface {
            public function setTeam(TeamInterface $team): MemberInterface
            {
                // TODO: Implement setTeam() method.
            }

            public function getTeam(): TeamInterface
            {
                // TODO: Implement getTeam() method.
            }
        };
        $security->method('getUser')->willReturn($user);

        $team = new class extends Team implements TimezoneAwareInterface {
            public function getTimezone(): \DateTimeZone
            {
                return new \DateTimeZone('Europe/Berlin');
            }

            public function hasTimezone(): bool
            {
                return true;
            }
        };
        $teamProvider->method('getCurrentTeam')->willReturn($team);

        date_default_timezone_set('UTC');

        $timezoneListener = new TimezoneListener($security, $teamProvider);
        $timezoneListener->onKernelRequest();

        $this->assertEquals('Europe/Berlin', date_default_timezone_get());
    }
}
