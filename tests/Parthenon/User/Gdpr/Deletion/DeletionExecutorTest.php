<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
