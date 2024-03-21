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

namespace Parthenon\AbTesting\Calculation;

use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\AbTesting\Entity\Variant;
use Parthenon\AbTesting\Repository\StatsRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ExperimentStatsCalculatorTest extends TestCase
{
    public function testReturnsCorrectStatsForCorrectResults()
    {
        $control = new Variant();
        $control->setName('control');
        $experimentVariant = new Variant();
        $experimentVariant->setName('experiment');

        $experiment = new Experiment();
        $experiment->setName('decisionId');
        $experiment->setDesiredResult('user_signup');
        $experiment->setVariants([$experimentVariant, $control]);
        $experiment->setType('user');

        $statsRepository = $this->createMock(StatsRepositoryInterface::class);

        $statsRepository->method('getCountOverallStatsOfExperiment')
            ->withConsecutive(
                [$this->equalTo('decisionId'), $this->equalTo('experiment')],
                [$this->equalTo('decisionId'), $this->equalTo('control')]
            )
            ->willReturnOnConsecutiveCalls(100, 93);

        $statsRepository->method('getConvertCountOverallStatsOfUserExperimentAndResult')
            ->withConsecutive(
                [$this->equalTo('decisionId'), $this->equalTo('experiment'), $this->equalTo('user_signup')],
                [$this->equalTo('decisionId'), $this->equalTo('control'), $this->equalTo('user_signup')]
            )
            ->willReturnOnConsecutiveCalls(25, 34);

        $calculator = new ExperimentStatsCalculator($statsRepository);
        $output = $calculator->getOverallStats($experiment);

        foreach ($output->getVariants() as $variant) {
            if ('control' === $variant->getName()) {
                $controlVariant = $variant;
            } elseif ('experiment' === $variant->getName()) {
                $experimentVariant = $variant;
            }
        }

        $this->assertEquals(93, $controlVariant->getStats()->getNumberOfSessions());
        $this->assertEquals(34, $controlVariant->getStats()->getNumberOfConversions());
        $this->assertEquals(36.56, $controlVariant->getStats()->getConversionPercentage());

        $this->assertEquals(100, $experimentVariant->getStats()->getNumberOfSessions());
        $this->assertEquals(25, $experimentVariant->getStats()->getNumberOfConversions());
        $this->assertEquals(25.0, $experimentVariant->getStats()->getConversionPercentage());
    }

    public function testReturnsCorrectStatsForCorrectResultsForSession()
    {
        $control = new Variant();
        $control->setName('control');
        $experimentVariant = new Variant();
        $experimentVariant->setName('experiment');

        $experiment = new Experiment();
        $experiment->setName('decisionId');
        $experiment->setDesiredResult('user_signup');
        $experiment->setVariants([$experimentVariant, $control]);
        $experiment->setType('session');

        $statsRepository = $this->createMock(StatsRepositoryInterface::class);

        $statsRepository->method('getCountOverallStatsOfExperiment')
            ->withConsecutive(
                [$this->equalTo('decisionId'), $this->equalTo('experiment')],
                [$this->equalTo('decisionId'), $this->equalTo('control')]
            )
            ->willReturnOnConsecutiveCalls(100, 93);

        $statsRepository->method('getConvertCountOverallStatsOfSessionExperimentAndResult')
            ->withConsecutive(
                [$this->equalTo('decisionId'), $this->equalTo('experiment'), $this->equalTo('user_signup')],
                [$this->equalTo('decisionId'), $this->equalTo('control'), $this->equalTo('user_signup')]
            )
            ->willReturnOnConsecutiveCalls(25, 34);

        $calculator = new ExperimentStatsCalculator($statsRepository);
        $output = $calculator->getOverallStats($experiment);

        foreach ($output->getVariants() as $variant) {
            if ('control' === $variant->getName()) {
                $controlVariant = $variant;
            } elseif ('experiment' === $variant->getName()) {
                $experimentVariant = $variant;
            }
        }

        $this->assertEquals(93, $controlVariant->getStats()->getNumberOfSessions());
        $this->assertEquals(34, $controlVariant->getStats()->getNumberOfConversions());
        $this->assertEquals(36.56, $controlVariant->getStats()->getConversionPercentage());

        $this->assertEquals(100, $experimentVariant->getStats()->getNumberOfSessions());
        $this->assertEquals(25, $experimentVariant->getStats()->getNumberOfConversions());
        $this->assertEquals(25.0, $experimentVariant->getStats()->getConversionPercentage());
    }
}
