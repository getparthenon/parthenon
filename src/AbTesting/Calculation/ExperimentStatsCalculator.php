<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
