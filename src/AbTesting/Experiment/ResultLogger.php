<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\AbTesting\Experiment;

use Parthenon\AbTesting\Decider\EnabledDecider\DecidedManagerInterface;
use Parthenon\AbTesting\Events\SessionCreator;
use Parthenon\AbTesting\Repository\ResultLogRepositoryInterface;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\User\Entity\UserInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class ResultLogger implements ResultLoggerInterface
{
    private ResultLogRepositoryInterface $resultLogRepository;
    private SessionInterface $session;
    private DecidedManagerInterface $enabledDecider;

    public function __construct(ResultLogRepositoryInterface $resultLogRepository, RequestStack $requestStack, DecidedManagerInterface $enabledDecider)
    {
        $this->resultLogRepository = $resultLogRepository;
        $this->session = $requestStack->getSession();
        $this->enabledDecider = $enabledDecider;
    }

    public function log(string $resultId, ?UserInterface $user = null, array $userAttributes = [], array $eventTags = []): void
    {
        if (!$this->enabledDecider->isTestable()) {
            return;
        }

        $sessionIdAsString = $this->session->get(SessionCreator::SESSION_ID);

        if (!$sessionIdAsString) {
            throw new GeneralException("Session doesn't exist");
        }

        $sessionId = Uuid::fromString($sessionIdAsString);

        $this->resultLogRepository->saveResult($sessionId, $resultId, $user);
    }
}
