<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
