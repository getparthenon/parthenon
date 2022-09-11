<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Calculation;

use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;

final class Calculate
{
    private ExperimentRepositoryInterface $experimentRepository;
    private ExperimentStatsCalculatorInterface $experimentStatsCalculator;

    public function __construct(ExperimentRepositoryInterface $experimentRepository, ExperimentStatsCalculatorInterface $experimentStatsCalculator)
    {
        $this->experimentRepository = $experimentRepository;
        $this->experimentStatsCalculator = $experimentStatsCalculator;
    }

    public function process(Experiment $experiment)
    {
        $experiment = $this->experimentStatsCalculator->getOverallStats($experiment);
        $this->experimentRepository->save($experiment);
    }
}
