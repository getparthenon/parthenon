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

namespace Parthenon\AbTesting\Experiment;

use Parthenon\AbTesting\Decider\EnabledDecider\DecidedManagerInterface;
use Parthenon\AbTesting\Events\SessionCreator;
use Parthenon\AbTesting\Repository\ResultLogRepositoryInterface;
use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ResultLoggerTest extends TestCase
{
    public function testCallsRepository()
    {
        $enabledDecider = $this->createMock(DecidedManagerInterface::class);
        $enabledDecider->method('isTestable')->willReturn(true);

        $repository = $this->createMock(ResultLogRepositoryInterface::class);

        $session = $this->createMock(SessionInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getSession')->willReturn($session);

        $uuid = Uuid::uuid4();
        $resultId = 'user_sign_up';
        $session->method('get')->with($this->equalTo(SessionCreator::SESSION_ID))->willReturn((string) $uuid);

        $repository->expects($this->once())->method('saveResult')->with($this->equalTo($uuid), $resultId, null);

        $resultLogger = new ResultLogger($repository, $requestStack, $enabledDecider);
        $resultLogger->log($resultId);
    }

    public function testCallsRepositoryWithUser()
    {
        $enabledDecider = $this->createMock(DecidedManagerInterface::class);
        $enabledDecider->method('isTestable')->willReturn(true);

        $repository = $this->createMock(ResultLogRepositoryInterface::class);

        $session = $this->createMock(SessionInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getSession')->willReturn($session);

        $user = new User();
        $uuid = Uuid::uuid4();
        $resultId = 'user_sign_up';
        $session->method('get')->with($this->equalTo(SessionCreator::SESSION_ID))->willReturn((string) $uuid);

        $repository->expects($this->once())->method('saveResult')->with($this->equalTo($uuid), $resultId, $user);

        $resultLogger = new ResultLogger($repository, $requestStack, $enabledDecider);
        $resultLogger->log($resultId, $user);
    }
}
