<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
