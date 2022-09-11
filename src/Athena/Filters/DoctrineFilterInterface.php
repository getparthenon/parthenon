<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Filters;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

interface DoctrineFilterInterface extends FilterInterface
{
    public function modifyQueryBuilder(QueryBuilder $queryBuilder);

    public function modifyQuery(Query $query);
}
