<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
