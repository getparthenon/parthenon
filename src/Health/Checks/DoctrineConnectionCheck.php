<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
