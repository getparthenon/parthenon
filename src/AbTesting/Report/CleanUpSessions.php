<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
