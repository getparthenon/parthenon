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
