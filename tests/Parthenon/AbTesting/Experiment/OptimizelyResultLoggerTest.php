<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\AbTesting\Experiment;

use Optimizely\Optimizely;
use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class OptimizelyResultLoggerTest extends TestCase
{
    public function testCallsClient()
    {
        $optimizely = $this->createMock(Optimizely::class);
        $optimizely->expects($this->once())->method('track')->with($this->equalTo('user_login'), $this->equalTo(null), $this->equalTo([]), $this->equalTo([]));

        $resultLogger = new OptimizelyResultLogger($optimizely);
        $resultLogger->log('user_login');
    }

    public function testCallsClientUser()
    {
        $user = new User();
        $user->setId('id');

        $optimizely = $this->createMock(Optimizely::class);
        $optimizely->expects($this->once())->method('track')->with($this->equalTo('user_login'), $this->equalTo('id'), $this->equalTo([]), $this->equalTo([]));

        $resultLogger = new OptimizelyResultLogger($optimizely);
        $resultLogger->log('user_login', $user);
    }

    public function testCallsClientUserWithOptions()
    {
        $user = new User();
        $user->setId('id');

        $optimizely = $this->createMock(Optimizely::class);
        $optimizely->expects($this->once())->method('track')->with($this->equalTo('user_login'), $this->equalTo('id'), $this->equalTo(['options' => true]), $this->equalTo([]));

        $resultLogger = new OptimizelyResultLogger($optimizely);
        $resultLogger->log('user_login', $user, ['options' => true]);
    }
}
