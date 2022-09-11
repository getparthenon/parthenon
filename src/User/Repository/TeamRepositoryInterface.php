<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Repository;

use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;

interface TeamRepositoryInterface extends CrudRepositoryInterface
{
    public function getByMember(MemberInterface $member): TeamInterface;
}
