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
