<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Filters;

use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;

interface OdmFilterInterface extends FilterInterface
{
    public function modifiyOdmQueryBuilder(QueryBuilder $builder): QueryBuilder;
}
