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

namespace Parthenon\AbTesting\Experiment;

use Parthenon\AbTesting\Decider\ChoiceDeciderInterface;
use Parthenon\AbTesting\Decider\EnabledDecider\DecidedManagerInterface;
use Parthenon\AbTesting\Events\SessionCreator;
use Parthenon\AbTesting\Repository\ExperimentLogRepositoryInterface;
use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\UserInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

final class Decider implements DeciderInterface
{
    private ExperimentLogRepositoryInterface $experimentLogRepository;
    private RequestStack $requestStack;
    private DecidedManagerInterface $enabledDecider;
    private ChoiceDeciderInterface $choiceDecider;
    private ExperimentRepositoryInterface $experimentRepository;

    public function __construct(ExperimentRepositoryInterface $experimentRepository, ExperimentLogRepositoryInterface $experimentLogRepository, RequestStack $requestStack, DecidedManagerInterface $enabledDecider, ChoiceDeciderInterface $deciderManager)
    {
        $this->experimentLogRepository = $experimentLogRepository;
        $this->requestStack = $requestStack;
        $this->enabledDecider = $enabledDecider;
        $this->choiceDecider = $deciderManager;
        $this->experimentRepository = $experimentRepository;
    }

    public function doExperiment(string $experimentName, ?UserInterface $user = null, array $options = []): string
    {
        try {
            $session = $this->requestStack->getSession();
        } catch (SessionNotFoundException $e) {
            return 'control';
        }

        if (!$this->enabledDecider->isTestable()) {
            return 'control';
        }

        $decision = $this->choiceDecider->getChoice($experimentName);

        if (!is_null($decision)) {
            return $decision;
        }

        $decision = $session->get('ab_testing_'.$experimentName);

        if (null === $decision) {
            $randomNumber = mt_rand(0, 100);
            $lowerBound = 0;
            try {
                $experiment = $this->experimentRepository->findByName($experimentName);
                $decision = 'control';
                foreach ($experiment->getVariants() as $variant) {
                    $upperBound = $lowerBound + $variant->getPercentage();
                    if (100 === $upperBound) {
                        $decision = $variant->getName();
                        break;
                    }
                    if ($randomNumber > $lowerBound && $randomNumber <= $upperBound) {
                        $decision = $variant->getName();
                        break;
                    }
                    $lowerBound = $upperBound;
                }
            } catch (NoEntityFoundException $e) {
                $decision = 'control';
            }

            $idAsString = $session->get(SessionCreator::SESSION_ID);
            $uuid = Uuid::fromString($idAsString);

            $this->experimentLogRepository->saveDecision($uuid, $experimentName, $decision);
            $session->set('ab_testing_'.$experimentName, $decision);
        }

        return $decision;
    }
}
