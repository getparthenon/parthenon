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

namespace Parthenon\AbTesting\Decider\ChoiceDecider;

use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\AbTesting\Entity\Variant;
use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CacheGeneratorTest extends TestCase
{
    public const DECISION = 'control';
    public const DECISION_ID = 'decision_id';

    public function testIsSetCache()
    {
        $experimentRepository = $this->createMock(ExperimentRepositoryInterface::class);

        $redis = $this->createMock(\Redis::class);

        $experiment = new Experiment();
        $experiment->setName(self::DECISION_ID);
        $variant = new Variant();
        $variant->setName(self::DECISION);
        $variant->setIsDefault(true);
        $experiment->setVariants([$variant]);

        $experimentTwo = new Experiment();

        $experimentRepository->method('findAll')->will($this->generate([$experiment, $experimentTwo]));

        $redis->expects($this->once())
            ->method('set')
            ->with($this->equalTo('abtesting_decision_cache'), $this->equalTo(json_encode([self::DECISION_ID => self::DECISION])));

        $cacheGenerator = new CacheGenerator($experimentRepository, $redis);
        $cacheGenerator->generate();
    }

    protected function generate(array $yield_values)
    {
        return $this->returnCallback(function () use ($yield_values) {
            foreach ($yield_values as $value) {
                yield $value;
            }
        });
    }
}
