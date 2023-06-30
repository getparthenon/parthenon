<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\AbTesting\Repository;

use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

interface ExperimentRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return \Generator|Experiment[]
     */
    public function findAll(): \Generator;

    /**
     * @throws NoEntityFoundException
     */
    public function findByName(string $name): Experiment;
}
