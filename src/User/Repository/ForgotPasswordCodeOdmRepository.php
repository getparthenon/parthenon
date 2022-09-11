<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Repository;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\OdmRepository;
use Parthenon\User\Entity\ForgotPasswordCode;

class ForgotPasswordCodeOdmRepository extends OdmRepository implements ForgotPasswordCodeRepositoryInterface
{
    public function findActiveByCode($code): ForgotPasswordCode
    {
        $passwordReset = $this->documentRepository->findOneBy(['code' => $code, 'used' => false]);

        if (!$passwordReset instanceof ForgotPasswordCode) {
            throw new NoEntityFoundException();
        }

        return $passwordReset;
    }
}
