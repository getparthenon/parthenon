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

use Parthenon\AbTesting\Decider\ChoiceDeciderInterface;
use Parthenon\AbTesting\Decider\EnabledDecider\DecidedManagerInterface;
use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\AbTesting\Entity\Variant;
use Parthenon\AbTesting\Events\SessionCreator;
use Parthenon\AbTesting\Repository\ExperimentLogRepositoryInterface;
use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DeciderTest extends TestCase
{
    public const EXPERIMENT_NAME = 'header_one';

    public function testExistsEarlyWhenNotEnabled()
    {
        $experimentRepository = $this->createMock(ExperimentRepositoryInterface::class);
        $experimentLogRepository = $this->createMock(ExperimentLogRepositoryInterface::class);

        $session = $this->createMock(SessionInterface::class);
        $requestStack = $this->createMock(RequestStack::class);

        $enablerDecider = $this->createMock(DecidedManagerInterface::class);
        $choiceDecider = $this->createMock(ChoiceDeciderInterface::class);

        $controlVariant = new Variant();
        $controlVariant->setName('control');
        $controlVariant->setPercentage(50);

        $experimentVariant = new Variant();
        $experimentVariant->setName('experiment');
        $experimentVariant->setPercentage(50);

        $experiment = new Experiment();
        $experiment->setVariants([$controlVariant, $experiment]);

        $requestStack->method('getSession')->willReturn($session);
        $enablerDecider->method('isTestable')->willReturn(false);
        $experimentLogRepository->expects($this->never())->method('saveDecision');

        $decider = new Decider($experimentRepository, $experimentLogRepository, $requestStack, $enablerDecider, $choiceDecider);
        $this->assertEquals('control', $decider->doExperiment(self::EXPERIMENT_NAME));
    }

    public function testExistsEarlyWhenChoiceIsMade()
    {
        $experimentRepository = $this->createMock(ExperimentRepositoryInterface::class);
        $experimentLogRepository = $this->createMock(ExperimentLogRepositoryInterface::class);

        $session = $this->createMock(SessionInterface::class);
        $requestStack = $this->createMock(RequestStack::class);

        $enablerDecider = $this->createMock(DecidedManagerInterface::class);
        $choiceDecider = $this->createMock(ChoiceDeciderInterface::class);

        $controlVariant = new Variant();
        $controlVariant->setName('control');
        $controlVariant->setPercentage(50);

        $experimentVariant = new Variant();
        $experimentVariant->setName('experiment');
        $experimentVariant->setPercentage(50);

        $experiment = new Experiment();
        $experiment->setVariants([$controlVariant, $experiment]);

        $requestStack->method('getSession')->willReturn($session);
        $enablerDecider->method('isTestable')->willReturn(true);
        $choiceDecider->method('getChoice')->willReturn('experiment');
        $experimentLogRepository->expects($this->never())->method('saveDecision');

        $decider = new Decider($experimentRepository, $experimentLogRepository, $requestStack, $enablerDecider, $choiceDecider);
        $this->assertEquals('experiment', $decider->doExperiment(self::EXPERIMENT_NAME));
    }

    public function testLogsDecision()
    {
        $uuid = Uuid::uuid4();
        $decisionId = self::EXPERIMENT_NAME;
        $experimentRepository = $this->createMock(ExperimentRepositoryInterface::class);
        $experimentLogRepository = $this->createMock(ExperimentLogRepositoryInterface::class);

        $session = $this->createMock(SessionInterface::class);
        $requestStack = $this->createMock(RequestStack::class);

        $enablerDecider = $this->createMock(DecidedManagerInterface::class);
        $choiceDecider = $this->createMock(ChoiceDeciderInterface::class);

        $controlVariant = new Variant();
        $controlVariant->setName('control');
        $controlVariant->setPercentage(50);

        $experimentVariant = new Variant();
        $experimentVariant->setName('experiment');
        $experimentVariant->setPercentage(50);

        $experiment = new Experiment();
        $experiment->setVariants([$controlVariant, $experimentVariant]);

        $experimentRepository->method('findByName')->with($this->equalTo(self::EXPERIMENT_NAME))->willReturn($experiment);

        $requestStack->method('getSession')->willReturn($session);
        $enablerDecider->method('isTestable')->willReturn(true);
        $choiceDecider->method('getChoice')->willReturn(null);

        $session->method('get')->withConsecutive([$this->equalTo('ab_testing_'.$decisionId)], [SessionCreator::SESSION_ID])->willReturnOnConsecutiveCalls(null, (string) $uuid);
        $session->expects($this->once())->method('set')->with($this->equalTo('ab_testing_'.$decisionId), $this->anything());

        $experimentLogRepository->expects($this->once())->method('saveDecision');

        $decider = new Decider($experimentRepository, $experimentLogRepository, $requestStack, $enablerDecider, $choiceDecider);
        $decider->doExperiment(self::EXPERIMENT_NAME);
    }

    public function testSameDecision()
    {
        $uuid = Uuid::uuid4();
        $decisionId = self::EXPERIMENT_NAME;
        $experimentRepository = $this->createMock(ExperimentRepositoryInterface::class);
        $experimentLogRepository = $this->createMock(ExperimentLogRepositoryInterface::class);

        $session = $this->createMock(SessionInterface::class);
        $requestStack = $this->createMock(RequestStack::class);

        $enablerDecider = $this->createMock(DecidedManagerInterface::class);
        $choiceDecider = $this->createMock(ChoiceDeciderInterface::class);

        $controlVariant = new Variant();
        $controlVariant->setName('control');
        $controlVariant->setPercentage(50);

        $experimentVariant = new Variant();
        $experimentVariant->setName('experiment');
        $experimentVariant->setPercentage(50);

        $experiment = new Experiment();
        $experiment->setVariants([$controlVariant, $experimentVariant]);

        $experimentRepository->method('findByName')->with($this->equalTo(self::EXPERIMENT_NAME))->willReturn($experiment);

        $requestStack->method('getSession')->willReturn($session);
        $enablerDecider->method('isTestable')->willReturn(true);
        $choiceDecider->method('getChoice')->willReturn(null);

        $session->method('get')->withConsecutive([$this->equalTo('ab_testing_'.$decisionId)], [SessionCreator::SESSION_ID])->willReturnOnConsecutiveCalls('control', (string) $uuid);
        $session->expects($this->never())->method('set')->with($this->equalTo('ab_testing_'.$decisionId), $this->anything());

        $experimentLogRepository->expects($this->never())->method('saveDecision');

        $decider = new Decider($experimentRepository, $experimentLogRepository, $requestStack, $enablerDecider, $choiceDecider);
        $decider->doExperiment(self::EXPERIMENT_NAME);
    }

    public function testControlIfNotFound()
    {
        $uuid = Uuid::uuid4();
        $decisionId = self::EXPERIMENT_NAME;
        $experimentRepository = $this->createMock(ExperimentRepositoryInterface::class);
        $experimentLogRepository = $this->createMock(ExperimentLogRepositoryInterface::class);

        $session = $this->createMock(SessionInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getSession')->willReturn($session);

        $enablerDecider = $this->createMock(DecidedManagerInterface::class);
        $choiceDecider = $this->createMock(ChoiceDeciderInterface::class);

        $controlVariant = new Variant();
        $controlVariant->setName('control');
        $controlVariant->setPercentage(50);

        $experimentVariant = new Variant();
        $experimentVariant->setName('experiment');
        $experimentVariant->setPercentage(50);

        $experiment = new Experiment();
        $experiment->setVariants([$controlVariant, $experimentVariant]);

        $experimentRepository->method('findByName')->with($this->equalTo(self::EXPERIMENT_NAME))->willThrowException(new NoEntityFoundException());

        $enablerDecider->method('isTestable')->willReturn(true);
        $choiceDecider->method('getChoice')->willReturn(null);

        $session->method('get')->withConsecutive([$this->equalTo('ab_testing_'.$decisionId)], [SessionCreator::SESSION_ID])->willReturnOnConsecutiveCalls('control', (string) $uuid);
        $session->expects($this->never())->method('set')->with($this->equalTo('ab_testing_'.$decisionId), $this->anything());

        $experimentLogRepository->expects($this->never())->method('saveDecision');

        $decider = new Decider($experimentRepository, $experimentLogRepository, $requestStack, $enablerDecider, $choiceDecider);
        $decider->doExperiment(self::EXPERIMENT_NAME);
    }
}
