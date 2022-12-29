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

namespace Parthenon\Funnel;

final class FunnelState
{
    private $entity;

    private int $step;

    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getStep(): int
    {
        return $this->step;
    }

    public function setStep($step): self
    {
        $this->step = $step;

        return $this;
    }
}
