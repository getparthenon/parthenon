<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Repository;

interface StatsRepositoryInterface
{
    public function getCountOverallStatsOfExperiment(string $decisionId, string $decisionOutput): int;

    public function getConvertCountOverallStatsOfSessionExperimentAndResult(string $decisionId, string $decisionOutput, string $result): int;

    public function getConvertCountOverallStatsOfUserExperimentAndResult(string $decisionId, string $decisionOutput, string $result): int;
}
