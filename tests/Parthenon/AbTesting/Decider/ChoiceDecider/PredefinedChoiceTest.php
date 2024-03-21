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

namespace Parthenon\AbTesting\Decider\ChoiceDecider;

use PHPUnit\Framework\TestCase;

class PredefinedChoiceTest extends TestCase
{
    public function testReturnsNullWhenNull()
    {
        $redis = $this->createMock(\Redis::class);

        $predefineChoice = new PredefinedChoice($redis);

        $this->assertNull($predefineChoice->getChoice('id'));
    }

    public function testReturnsNullWhenNulCalledOnlyOnce()
    {
        $redis = $this->createMock(\Redis::class);

        $redis->expects($this->once())
            ->method('get')
            ->with($this->equalTo('abtesting_decision_cache'))
            ->willReturn(null);

        $predefineChoice = new PredefinedChoice($redis);

        $predefineChoice->getChoice('id');
        $predefineChoice->getChoice('id');
    }

    public function testReturnsNullWhenInvalid()
    {
        $redis = $this->createMock(\Redis::class);

        $redis->expects($this->once())
            ->method('get')
            ->with($this->equalTo('abtesting_decision_cache'))
            ->willReturn('#');

        $predefineChoice = new PredefinedChoice($redis);

        $this->assertNull($predefineChoice->getChoice('id'));
    }

    public function testReturnsNullWhenNotDefined()
    {
        $redis = $this->createMock(\Redis::class);

        $redis->expects($this->once())
            ->method('get')
            ->with($this->equalTo('abtesting_decision_cache'))
            ->willReturn(json_encode([]));

        $predefineChoice = new PredefinedChoice($redis);

        $this->assertNull($predefineChoice->getChoice('id'));
    }

    public function testReturnsNullWhenNotValidChoice()
    {
        $redis = $this->createMock(\Redis::class);

        $redis->expects($this->once())
            ->method('get')
            ->with($this->equalTo('abtesting_decision_cache'))
            ->willReturn(json_encode(['id' => 'cool']));

        $predefineChoice = new PredefinedChoice($redis);

        $this->assertNull($predefineChoice->getChoice('id'));
    }

    public function testReturnsControl()
    {
        $redis = $this->createMock(\Redis::class);

        $redis->expects($this->once())
            ->method('get')
            ->with($this->equalTo('abtesting_decision_cache'))
            ->willReturn(json_encode(['id' => 'control']));

        $predefineChoice = new PredefinedChoice($redis);

        $this->assertEquals('control', $predefineChoice->getChoice('id'));
    }

    public function testReturnsExperiment()
    {
        $redis = $this->createMock(\Redis::class);

        $redis->expects($this->once())
            ->method('get')
            ->with($this->equalTo('abtesting_decision_cache'))
            ->willReturn(json_encode(['id' => 'experiment']));

        $predefineChoice = new PredefinedChoice($redis);

        $this->assertEquals('experiment', $predefineChoice->getChoice('id'));
    }
}
