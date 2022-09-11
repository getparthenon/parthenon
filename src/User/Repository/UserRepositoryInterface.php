<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Repository;

use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\RepositoryInterface;
use Parthenon\User\Entity\User;

interface UserRepositoryInterface extends RepositoryInterface, CrudRepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function findByEmail($user): User;

    /**
     * @throws NoEntityFoundException
     */
    public function findByConfirmationCode(string $confirmationCode): User;

    public function getUserSignupStats(): array;
}
