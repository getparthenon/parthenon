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
