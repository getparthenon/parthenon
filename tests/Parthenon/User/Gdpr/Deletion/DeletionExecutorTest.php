<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Deletion;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class DeletionExecutorTest extends TestCase
{
    public function testCallsDeleterWhenDeciderIsTrue()
    {
        $voter = $this->createMock(VoterInterface::class);
        $deleter = $this->createMock(DeleterInterface::class);
        $user = new User();

        $voter->method('canDelete')->with($this->equalTo($user))->will($this->returnValue(true));
        $deleter->expects($this->once())->method('delete')->with($this->equalTo($user));

        $deletionExecutor = new DeletionExecutor($voter, $deleter);
        $deletionExecutor->delete($user);
    }

    public function testDoesntCallsDeleterWhenDeciderIsFalse()
    {
        $voter = $this->createMock(VoterInterface::class);
        $deleter = $this->createMock(DeleterInterface::class);
        $user = new User();

        $voter->method('canDelete')->with($this->equalTo($user))->will($this->returnValue(false));
        $deleter->expects($this->never())->method('delete')->with($this->equalTo($user));

        $deletionExecutor = new DeletionExecutor($voter, $deleter);
        $deletionExecutor->delete($user);
    }
}
