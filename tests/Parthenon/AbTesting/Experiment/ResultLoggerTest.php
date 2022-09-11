<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
