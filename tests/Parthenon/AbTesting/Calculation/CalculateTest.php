<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\AbTesting\Calculation;

use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CalculateTest extends TestCase
{
    public function testCallsAndSaves()
    {
        $experimentRepository = $this->createMock(ExperimentRepositoryInterface::class);
        $experimentStatsCalculator = $this->createMock(ExperimentStatsCalculatorInterface::class);

        $experiment = new Experiment();

        $experimentRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($experiment));

        $experimentStatsCalculator->expects($this->once())
            ->method('getOverallStats')
            ->with($this->equalTo($experiment))
            ->will($this->returnValue($experiment));

        $calculate = new Calculate($experimentRepository, $experimentStatsCalculator);
        $calculate->process($experiment);
    }
}
