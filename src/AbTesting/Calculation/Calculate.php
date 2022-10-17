<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
