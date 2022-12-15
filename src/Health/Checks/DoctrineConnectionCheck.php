<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Health\Checks;

use Doctrine\ORM\EntityManagerInterface;

final class DoctrineConnectionCheck implements CheckInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getName(): string
    {
        return 'doctrine';
    }

    public function getStatus(): bool
    {
        try {
            $connection = $this->entityManager->getConnection();

            if (!$connection->isConnected()) {
                if (!$connection->connect()) {
                    return false;
                }
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getLevel(): string
    {
        return CheckInterface::LEVEL_CRITICAL;
    }
}
