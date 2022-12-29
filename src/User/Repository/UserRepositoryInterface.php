<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
