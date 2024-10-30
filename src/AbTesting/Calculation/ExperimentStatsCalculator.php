<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\AbTesting\Calculation;

use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\AbTesting\Entity\VariantStats;
use Parthenon\AbTesting\Repository\StatsRepositoryInterface;

final class ExperimentStatsCalculator implements ExperimentStatsCalculatorInterface
{
    private StatsRepositoryInterface $statsRepository;

    public function __construct(StatsRepositoryInterface $statsRepository)
    {
        $this->statsRepository = $statsRepository;
    }

    public function getOverallStats(Experiment $experiment): Experiment
    {
        foreach ($experiment->getVariants() as $variant) {
            $variantStats = new VariantStats();
            $sessionCount = $this->statsRepository->getCountOverallStatsOfExperiment($experiment->getName(), $variant->getName());
            if ($experiment->isUserBased()) {
                $convertedCount = $this->statsRepository->getConvertCountOverallStatsOfUserExperimentAndResult($experiment->getName(), $variant->getName(), $experiment->getDesiredResult());
            } else {
                $convertedCount = $this->statsRepository->getConvertCountOverallStatsOfSessionExperimentAndResult($experiment->getName(), $variant->getName(), $experiment->getDesiredResult());
            }
            $conversionPercentage = ($sessionCount > 0) ? (($convertedCount / $sessionCount) * 100) : 0;
            $conversionPercentage = round($conversionPercentage, 2);

            $variantStats->setNumberOfSessions($sessionCount);
            $variantStats->setNumberOfConversions($convertedCount);
            $variantStats->setConversionPercentage($conversionPercentage);

            $variant->setStats($variantStats);
        }

        return $experiment;
    }
}
