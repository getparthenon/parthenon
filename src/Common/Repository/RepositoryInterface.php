<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Repository;

interface RepositoryInterface
{
    public function findById($id);

    public function save($entity);
}
