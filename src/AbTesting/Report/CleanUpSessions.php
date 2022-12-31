<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\AbTesting\Report;

use Parthenon\AbTesting\Repository\ExperimentLogRepositoryInterface;
use Parthenon\AbTesting\Repository\ResultLogRepositoryInterface;
use Parthenon\AbTesting\Repository\SessionRepositoryInterface;
use Ramsey\Uuid\Uuid;

final class CleanUpSessions
{
    private SessionRepositoryInterface $sessionRepository;
    private ResultLogRepositoryInterface $resultLogRepository;
    private ExperimentLogRepositoryInterface $experimentLogRepository;
    private array $disabledUserAgents;

    public function __construct(
        SessionRepositoryInterface $sessionRepository,
        ResultLogRepositoryInterface $resultLogRepository,
        ExperimentLogRepositoryInterface $experimentLogRepository,
        array $disabledUserAgents
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->resultLogRepository = $resultLogRepository;
        $this->experimentLogRepository = $experimentLogRepository;
        $this->disabledUserAgents = $disabledUserAgents;
    }

    public function cleanUp(): void
    {
        foreach ($this->sessionRepository->findAll() as $row) {
            foreach ($this->disabledUserAgents as $userAgentPattern) {
                if (preg_match('~'.$userAgentPattern.'~', $row['user_agent'])) {
                    $sessionId = Uuid::fromString($row['id']);
                    $this->resultLogRepository->deleteAllForSession($sessionId);
                    $this->experimentLogRepository->deleteAllForSession($sessionId);
                    $this->sessionRepository->deleteSession($sessionId);
                }
            }
        }
    }
}
