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
