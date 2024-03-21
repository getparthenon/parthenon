<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
