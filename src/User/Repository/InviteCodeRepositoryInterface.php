<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Repository;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\RepositoryInterface;
use Parthenon\User\Entity\InviteCode;

interface InviteCodeRepositoryInterface extends RepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function findActiveByCode(string $code): InviteCode;
}
