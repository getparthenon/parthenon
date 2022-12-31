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

namespace Parthenon\User\Logging\Monolog;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidFactory;
use Symfony\Component\Security\Core\Security;

class UserProcessorTest extends TestCase
{
    public function testAddsUserId()
    {
        $security = $this->createMock(Security::class);
        $user = new User();
        $uuid = (new UuidFactory())->fromInteger('122');
        $user->setId($uuid);

        $security->method('getUser')->will($this->returnValue($user));

        $userProcessor = new UserProcessor($security);
        $actual = $userProcessor(['extra' => []]);
        $expected = ['extra' => ['user_id' => $uuid->toString()]];

        $this->assertEquals($expected, $actual);
    }
}
