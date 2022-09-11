<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Repository;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;
use Parthenon\User\Entity\ForgotPasswordCode;

class ForgotPasswordCodeRepository extends DoctrineRepository implements ForgotPasswordCodeRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActiveByCode($code): ForgotPasswordCode
    {
        $passwordReset = $this->entityRepository->findOneBy(['code' => $code, 'used' => false]);

        if (!$passwordReset) {
            throw new NoEntityFoundException();
        }

        return $passwordReset;
    }
}
