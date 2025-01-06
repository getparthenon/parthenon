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

namespace Parthenon\AbTesting\Report;

use Parthenon\AbTesting\Repository\ExperimentLogRepositoryInterface;
use Parthenon\AbTesting\Repository\ResultLogRepositoryInterface;
use Parthenon\AbTesting\Repository\SessionRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CleanUpSessionsTest extends TestCase
{
    public function testCallsDeleteForBlocked()
    {
        $uuidOne = Uuid::uuid4();
        $rowOne = ['id' => $uuidOne->toString(), 'user_agent' => 'A-Good-User-Agent'];
        $uuidTwo = Uuid::uuid4();
        $rowTwo = ['id' => $uuidTwo->toString(), 'user_agent' => 'That Googlebot user agent'];

        $sessionRepository = $this->createMock(SessionRepositoryInterface::class);
        $resultLogRepository = $this->createMock(ResultLogRepositoryInterface::class);
        $experimentLogRepository = $this->createMock(ExperimentLogRepositoryInterface::class);

        $sessionRepository->expects($this->once())->method('deleteSession')->with($this->equalTo($uuidTwo));
        $resultLogRepository->expects($this->once())->method('deleteAllForSession')->with($this->equalTo($uuidTwo));
        $experimentLogRepository->expects($this->once())->method('deleteAllForSession')->with($this->equalTo($uuidTwo));

        $sessionRepository->method('findAll')->will($this->generate([$rowOne, $rowTwo]));

        $cleanupSessions = new CleanUpSessions($sessionRepository, $resultLogRepository, $experimentLogRepository, ['Googlebot']);
        $cleanupSessions->cleanUp();
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
