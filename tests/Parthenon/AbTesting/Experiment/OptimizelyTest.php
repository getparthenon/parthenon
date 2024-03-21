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

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class OptimizelyTest extends TestCase
{
    public function testReturnsControl()
    {
        $optimizely = $this->createMock(\Optimizely\Optimizely::class);

        $optimizely->method('activate')->with($this->equalTo('experiment_key'), $this->equalTo(null), $this->equalTo([]))->willReturn('control');

        $decider = new OptimizelyDecider($optimizely);
        $this->assertEquals('control', $decider->doExperiment('experiment_key'));
    }

    public function testReturnsControlWithUser()
    {
        $optimizely = $this->createMock(\Optimizely\Optimizely::class);

        $user = new User();
        $user->setId('id');

        $optimizely->method('activate')->with($this->equalTo('experiment_key'), $this->equalTo('id'), $this->equalTo([]))->willReturn('control');

        $decider = new OptimizelyDecider($optimizely);
        $this->assertEquals('control', $decider->doExperiment('experiment_key', $user));
    }
}
