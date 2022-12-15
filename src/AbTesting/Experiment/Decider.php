<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
