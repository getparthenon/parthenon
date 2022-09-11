<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Dbal;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

interface SchemaToolProviderInterface
{
    public function getSchemaTool(EntityManager $entityManager): SchemaTool;
}
