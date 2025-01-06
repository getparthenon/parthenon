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

namespace Parthenon\AbTesting\Events;

use Parthenon\AbTesting\Decider\EnabledDecider\DecidedManagerInterface;
use Parthenon\AbTesting\Repository\SessionRepositoryInterface;
use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SessionCreatorTest extends TestCase
{
    public function testIfNoSessionIdCreateASession()
    {
        $userAgent = 'User';
        $ipAddress = '127.0.0.1';
        $uuid = Uuid::uuid4();

        $session = $this->createMock(SessionInterface::class);
        $sessionRepository = $this->createMock(SessionRepositoryInterface::class);
        $requestEvent = $this->createMock(RequestEvent::class);
        $decidedManagerInterface = $this->createMock(DecidedManagerInterface::class);
        $request = $this->createMock(Request::class);
        $headersBag = $this->createMock(HeaderBag::class);

        $requestEvent->method('getRequest')->willReturn($request);

        $request->method('getSession')->willReturn($session);

        $decidedManagerInterface->method('isTestable')->willReturn(true);

        $request->headers = $headersBag;
        $request->method('getClientIp')->willReturn($ipAddress);
        $headersBag->method('get')->with($this->equalTo('User-Agent'), $this->equalTo('No-User-Agent-Given'))->willReturn($userAgent);

        $session->method('has')->with($this->equalTo(SessionCreator::SESSION_ID))->willReturn(false);
        $session->expects($this->once())->method('set')->with($this->equalTo(SessionCreator::SESSION_ID), $this->equalTo((string) $uuid));

        $sessionRepository->method('createSession')->with($this->equalTo($userAgent), $this->equalTo($ipAddress))->willReturn($uuid);

        $sessionCreator = new SessionCreator($sessionRepository, $decidedManagerInterface);
        $sessionCreator->onKernelRequest($requestEvent);
    }

    public function testIfNoSessionCreatedIfOneExists()
    {
        $userAgent = 'User';
        $ipAddress = '127.0.0.1';
        $uuid = Uuid::uuid4();

        $session = $this->createMock(SessionInterface::class);
        $sessionRepository = $this->createMock(SessionRepositoryInterface::class);
        $requestEvent = $this->createMock(RequestEvent::class);
        $decidedManagerInterface = $this->createMock(DecidedManagerInterface::class);
        $request = $this->createMock(Request::class);
        $headersBag = $this->createMock(HeaderBag::class);

        $requestEvent->method('getRequest')->willReturn($request);

        $request->method('getSession')->willReturn($session);

        $decidedManagerInterface->method('isTestable')->willReturn(true);

        $request->headers = $headersBag;
        $request->method('getClientIp')->willReturn($ipAddress);
        $headersBag->method('get')->with($this->equalTo('User-Agent'), $this->equalTo('No-User-Agent-Given'))->willReturn($userAgent);

        $session->method('has')->with($this->equalTo(SessionCreator::SESSION_ID))->willReturn(true);
        $session->expects($this->never())->method('set')->with($this->equalTo(SessionCreator::SESSION_ID), $this->equalTo((string) $uuid));

        $sessionRepository->method('createSession')->with($this->equalTo($userAgent), $this->equalTo($ipAddress))->willReturn($uuid);

        $sessionCreator = new SessionCreator($sessionRepository, $decidedManagerInterface);
        $sessionCreator->onKernelRequest($requestEvent);
    }

    public function testAttachUserToSession()
    {
        \DG\BypassFinals::enable();
        $uuid = Uuid::uuid4();

        $session = $this->createMock(SessionInterface::class);
        $requestEvent = $this->createMock(RequestEvent::class);
        $request = $this->createMock(Request::class);
        $sessionRepository = $this->createMock(SessionRepositoryInterface::class);
        $interactiveLogin = $this->createMock(InteractiveLoginEvent::class);
        $decidedManagerInterface = $this->createMock(DecidedManagerInterface::class);
        $token = $this->createMock(TokenInterface::class);

        $token->method('getUser')->willReturn(new User());
        $interactiveLogin->method('getAuthenticationToken')->willReturn($token);

        $interactiveLogin->method('getRequest')->willReturn($request);

        $request->method('getSession')->willReturn($session);

        $decidedManagerInterface->method('isTestable')->willReturn(true);

        $session->expects($this->once())->method('get')->with($this->equalTo(SessionCreator::SESSION_ID))->willReturn((string) $uuid);

        $sessionRepository->method('attachUserToSession')->with($this->equalTo($uuid), $this->isInstanceOf(User::class));

        $sessionCreator = new SessionCreator($sessionRepository, $decidedManagerInterface);
        $sessionCreator->onInteractiveLogin($interactiveLogin);
    }
}
