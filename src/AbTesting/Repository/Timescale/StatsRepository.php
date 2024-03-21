<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\AbTesting\Repository\Timescale;

use Doctrine\DBAL\Connection;
use Parthenon\AbTesting\Repository\StatsRepositoryInterface;

class StatsRepository implements StatsRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getCountOverallStatsOfExperiment(string $decisionId, string $decisionOutput): int
    {
        $statement = $this->connection->prepare('SELECT count(*) FROM ab_experiment_log WHERE decision_string_id = :decisionId AND decision_output = :decision');
        $statement->bindValue(':decisionId', $decisionId);
        $statement->bindValue(':decision', $decisionOutput);
        $result = $statement->executeQuery();

        return $result->fetchOne();
    }

    public function getConvertCountOverallStatsOfSessionExperimentAndResult(string $decisionId, string $decisionOutput, string $result): int
    {
        $statement = $this->connection->prepare('SELECT count(*) FROM ab_result_log WHERE result_string_id = :resultLog AND session_id IN (SELECT session_id FROM ab_experiment_log WHERE decision_string_id = :decisionId AND decision_output = :decision)');

        $statement->bindValue(':decisionId', $decisionId);
        $statement->bindValue(':decision', $decisionOutput);
        $statement->bindValue(':resultLog', $result);
        $result = $statement->executeQuery();

        return $result->fetchOne();
    }

    public function getConvertCountOverallStatsOfUserExperimentAndResult(string $decisionId, string $decisionOutput, string $result): int
    {
        $statement = $this->connection->prepare('SELECT count(*) FROM ab_result_log WHERE result_string_id = :resultLog AND user_id IN (select abas.user_id from ab_experiment_log ael inner join ab_sessions abas on abas.id = ael.session_id  WHERE ael.decision_string_id = :decisionId AND ael.decision_output = :decision)');

        $statement->bindValue(':decisionId', $decisionId);
        $statement->bindValue(':decision', $decisionOutput);
        $statement->bindValue(':resultLog', $result);
        $result = $statement->executeQuery();

        return $result->fetchOne();
    }
}
