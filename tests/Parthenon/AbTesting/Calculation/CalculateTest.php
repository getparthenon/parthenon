<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
