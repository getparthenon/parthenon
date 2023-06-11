<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Creator;

use Parthenon\Notification\EmailSenderInterface;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\User;
use Parthenon\User\Event\PostUserSignupEvent;
use Parthenon\User\Event\PreUserSignupEvent;
use Parthenon\User\Notification\MessageFactory;
use Parthenon\User\Notification\UserEmail;
use Parthenon\User\Repository\UserRepositoryInterface;
use Parthenon\User\Team\TeamCreatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserCreatorTest extends TestCase
{
    public const ENCODED_PASSWORD = 'EncodedPassword';
    public const RANDOM_PASSWORD = 'RandomPassword';
    public const CODE = 'code';

    public function testEncodesPasswordAndSaves()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordHasherFactory = $this->createMock(PasswordHasherFactoryInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $mainInviteHandler = $this->createMock(MainInviteHandlerInterface::class);
        $emailSender = $this->createMock(EmailSenderInterface::class);
        $messageFactory = $this->createMock(MessageFactory::class);
        $teamCreator = $this->createMock(TeamCreatorInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $request = $this->createMock(Request::class);
        $passwordHasher = $this->createMock(PasswordHasherInterface::class);

        $user = new class() extends User implements MemberInterface {
            private TeamInterface $team;

            public function setTeam(TeamInterface $team): MemberInterface
            {
                $this->team = $team;

                return $this;
            }

            public function getTeam(): TeamInterface
            {
                return $this->team;
            }
        };
        $user->setPassword(self::RANDOM_PASSWORD);
        $user->setName('User');
        $user->setEmail('parthenon.user@example.org');

        $eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->isInstanceOf(PreUserSignupEvent::class), $this->equalTo(PreUserSignupEvent::NAME)],
                [$this->isInstanceOf(PostUserSignupEvent::class), $this->equalTo(PostUserSignupEvent::NAME)],
            );

        $messageFactory->expects($this->never())
            ->method('getUserSignUpMessage')
            ->with($this->isInstanceOf(User::class))
            ->will($this->returnValue(UserEmail::createFromUser($user)));

        $passwordHasherFactory->method('getPasswordHasher')
            ->with($this->isInstanceOf(User::class))
            ->will($this->returnValue($passwordHasher));

        $passwordHasher->method('hash')->with($this->equalTo(self::RANDOM_PASSWORD))->will($this->returnValue(self::ENCODED_PASSWORD));

        $userRepository->expects($this->exactly(2))->method('save')->with($this->callback(function (User $user) {
            return self::ENCODED_PASSWORD === $user->getPassword();
        }));

        $teamCreator->expects($this->exactly(1))->method('createForUser')->with($this->isInstanceOf(User::class));
        $requestStack->method('getMainRequest')->will($this->returnValue($request));
        $request->method('get')->with($this->equalTo(self::CODE))->will($this->returnValue('invite_code'));

        $mainInviteHandler->expects($this->exactly(1))->method('handleInvite')->with($this->isInstanceOf(User::class), $this->equalTo('invite_code'));

        $userCreator = new UserCreator(
            $userRepository,
            $passwordHasherFactory,
            $eventDispatcher,
            $mainInviteHandler,
            $emailSender,
            $messageFactory,
            true,
            $teamCreator,
            $requestStack,
            'USER_ROLE',
            true
        );

        $userCreator->create($user);
    }

    public function testEncodesPasswordAndSavesNoInvite()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordHasherFactory = $this->createMock(PasswordHasherFactoryInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $mainInviteHandler = $this->createMock(MainInviteHandlerInterface::class);
        $emailSender = $this->createMock(EmailSenderInterface::class);
        $messageFactory = $this->createMock(MessageFactory::class);
        $teamCreator = $this->createMock(TeamCreatorInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $request = $this->createMock(Request::class);
        $passwordHasher = $this->createMock(PasswordHasherInterface::class);

        $user = new class() extends User implements MemberInterface {
            private TeamInterface $team;

            public function setTeam(TeamInterface $team): MemberInterface
            {
                $this->team = $team;

                return $this;
            }

            public function getTeam(): TeamInterface
            {
                return $this->team;
            }
        };
        $user->setPassword(self::RANDOM_PASSWORD);
        $user->setName('User');
        $user->setEmail('parthenon.user@example.org');

        $eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->isInstanceOf(PreUserSignupEvent::class), $this->equalTo(PreUserSignupEvent::NAME)],
                [$this->isInstanceOf(PostUserSignupEvent::class), $this->equalTo(PostUserSignupEvent::NAME)],
            );

        $messageFactory->expects($this->once())
            ->method('getUserSignUpMessage')
            ->with($this->isInstanceOf(User::class))
            ->will($this->returnValue(UserEmail::createFromUser($user)));

        $passwordHasherFactory->method('getPasswordHasher')
            ->with($this->isInstanceOf(User::class))
            ->will($this->returnValue($passwordHasher));

        $passwordHasher->method('hash')->with($this->equalTo(self::RANDOM_PASSWORD))->will($this->returnValue(self::ENCODED_PASSWORD));

        $userRepository->expects($this->exactly(2))->method('save')->with($this->callback(function (User $user) {
            return self::ENCODED_PASSWORD === $user->getPassword();
        }));

        $teamCreator->expects($this->exactly(1))->method('createForUser')->with($this->isInstanceOf(User::class));
        $requestStack->method('getMainRequest')->will($this->returnValue($request));
        $request->method('get')->with($this->equalTo(self::CODE))->will($this->returnValue(null));

        $mainInviteHandler->expects($this->never())->method('handleInvite')->with($this->isInstanceOf(User::class), $this->equalTo('invite_code'));

        $userCreator = new UserCreator(
            $userRepository,
            $passwordHasherFactory,
            $eventDispatcher,
            $mainInviteHandler,
            $emailSender,
            $messageFactory,
            true,
            $teamCreator,
            $requestStack,
            'USER_ROLE',
            true,
        );

        $emailSender->expects($this->once())->method('send')->with($this->isInstanceOf(UserEmail::class));

        $userCreator->create($user);
    }

    public function testEncodesPasswordAndSavesNoInviteNoTeam()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordHasherFactory = $this->createMock(PasswordHasherFactoryInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $mainInviteHandler = $this->createMock(MainInviteHandlerInterface::class);
        $emailSender = $this->createMock(EmailSenderInterface::class);
        $messageFactory = $this->createMock(MessageFactory::class);
        $teamCreator = $this->createMock(TeamCreatorInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $request = $this->createMock(Request::class);
        $passwordHasher = $this->createMock(PasswordHasherInterface::class);

        $user = new class() extends User implements MemberInterface {
            private TeamInterface $team;

            public function setTeam(TeamInterface $team): MemberInterface
            {
                $this->team = $team;

                return $this;
            }

            public function getTeam(): TeamInterface
            {
                return $this->team;
            }
        };
        $user->setPassword(self::RANDOM_PASSWORD);
        $user->setName('User');
        $user->setEmail('parthenon.user@example.org');

        $eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->isInstanceOf(PreUserSignupEvent::class), $this->equalTo(PreUserSignupEvent::NAME)],
                [$this->isInstanceOf(PostUserSignupEvent::class), $this->equalTo(PostUserSignupEvent::NAME)],
            );

        $messageFactory->expects($this->once())
            ->method('getUserSignUpMessage')
            ->with($this->isInstanceOf(User::class))
            ->will($this->returnValue(UserEmail::createFromUser($user)));

        $passwordHasherFactory->method('getPasswordHasher')
            ->with($this->isInstanceOf(User::class))
            ->will($this->returnValue($passwordHasher));

        $passwordHasher->method('hash')->with($this->equalTo(self::RANDOM_PASSWORD))->will($this->returnValue(self::ENCODED_PASSWORD));

        $userRepository->expects($this->exactly(2))->method('save')->with($this->callback(function (User $user) {
            return self::ENCODED_PASSWORD === $user->getPassword();
        }));

        $teamCreator->expects($this->exactly(0))->method('createForUser')->with($this->isInstanceOf(User::class));
        $requestStack->method('getMainRequest')->will($this->returnValue($request));
        $request->method('get')->with($this->equalTo(self::CODE))->will($this->returnValue(null));

        $mainInviteHandler->expects($this->never())->method('handleInvite')->with($this->isInstanceOf(User::class), $this->equalTo('invite_code'));

        $userCreator = new UserCreator(
            $userRepository,
            $passwordHasherFactory,
            $eventDispatcher,
            $mainInviteHandler,
            $emailSender,
            $messageFactory,
            false,
            $teamCreator,
            $requestStack,
            'USER_ROLE',
            true,
        );

        $emailSender->expects($this->once())->method('send')->with($this->isInstanceOf(UserEmail::class));

        $userCreator->create($user);
    }
}
