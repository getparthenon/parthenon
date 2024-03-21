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

namespace Parthenon\Payments\Subscriber;

use Parthenon\Payments\Entity\Subscription;
use Parthenon\Payments\Exception\InvalidSubscriberException;
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\User;
use Parthenon\User\Team\CurrentTeamProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class CurrentSubscriberProviderTest extends TestCase
{
    public function testRturnsUser()
    {
        $security = $this->createMock(Security::class);
        $currentTeamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new class() extends User implements SubscriberInterface {
            public function setSubscription(Subscription $subscription)
            {
                // TODO: Implement setSubscription() method.
            }

            public function getSubscription(): ?Subscription
            {
                // TODO: Implement getSubscription() method.
            }

            public function hasActiveSubscription(): bool
            {
                return false;
            }

            public function getIdentifier(): string
            {
                return '';
            }
        };

        $security->method('getUser')->willReturn($user);

        $provider = new CurrentSubscriberProvider(SubscriberInterface::TYPE_USER, $security, $currentTeamProvider);
        $this->assertSame($user, $provider->getSubscriber());
    }

    public function testThrowsExceptionNoUser()
    {
        $this->expectException(InvalidSubscriberException::class);
        $security = $this->createMock(Security::class);
        $currentTeamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new class() extends User {};

        $security->method('getUser')->willReturn($user);

        $provider = new CurrentSubscriberProvider(SubscriberInterface::TYPE_USER, $security, $currentTeamProvider);
        $provider->getSubscriber();
    }

    public function testRturnsTeam()
    {
        $security = $this->createMock(Security::class);
        $currentTeamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $team = new class() extends Team implements SubscriberInterface {
            public function setSubscription(Subscription $subscription)
            {
                // TODO: Implement setSubscription() method.
            }

            public function getSubscription(): ?Subscription
            {
                // TODO: Implement getSubscription() method.
            }

            public function hasActiveSubscription(): bool
            {
                return false;
            }

            public function getIdentifier(): string
            {
                return '';
            }
        };
        $currentTeamProvider->method('getCurrentTeam')->willReturn($team);

        $provider = new CurrentSubscriberProvider(SubscriberInterface::TYPE_TEAM, $security, $currentTeamProvider);
        $this->assertSame($team, $provider->getSubscriber());
    }

    public function testThrowsExceptionInvalidTeam()
    {
        $this->expectException(InvalidSubscriberException::class);
        $security = $this->createMock(Security::class);
        $currentTeamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $team = new class() extends Team {};
        $currentTeamProvider->method('getCurrentTeam')->willReturn($team);

        $provider = new CurrentSubscriberProvider(SubscriberInterface::TYPE_TEAM, $security, $currentTeamProvider);
        $provider->getSubscriber();
    }

    public function testThrowsExceptionInvalidType()
    {
        $this->expectException(InvalidSubscriberException::class);
        $security = $this->createMock(Security::class);
        $currentTeamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $provider = new CurrentSubscriberProvider('', $security, $currentTeamProvider);
        $provider->getSubscriber();
    }
}
