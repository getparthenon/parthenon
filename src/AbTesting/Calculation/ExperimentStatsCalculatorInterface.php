<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Calculation;

use Parthenon\AbTesting\Entity\Experiment;

interface ExperimentStatsCalculatorInterface
{
    public function getOverallStats(Experiment $experiment): Experiment;
}
