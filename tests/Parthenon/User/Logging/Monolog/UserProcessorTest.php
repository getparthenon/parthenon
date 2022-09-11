<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
