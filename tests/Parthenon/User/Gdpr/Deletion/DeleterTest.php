<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Deletion;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class DeleterTest extends TestCase
{
    public function testReturnsTrueIfAllVotesAreTrue()
    {
        $user = new User();

        $deleterOne = $this->createMock(DeleterInterface::class);
        $deleterTwo = $this->createMock(DeleterInterface::class);

        $deleterOne->expects($this->once())->method('delete')->with($this->equalTo($user))->willReturn(true);
        $deleterTwo->expects($this->once())->method('delete')->with($this->equalTo($user))->willReturn(true);

        $deleter = new Deleter();
        $deleter->add($deleterOne)->add($deleterTwo);

        $deleter->delete($user);
    }
}
