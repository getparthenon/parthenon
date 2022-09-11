<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
