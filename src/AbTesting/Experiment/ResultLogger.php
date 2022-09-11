<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
