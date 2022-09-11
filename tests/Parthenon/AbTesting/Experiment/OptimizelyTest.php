<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
