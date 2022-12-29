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

namespace Parthenon\Export\Normaliser;

use Parthenon\Export\Exception\NoNormaliserFoundException;

final class NormaliserManager implements NormaliserManagerInterface
{
    /**
     * @param NormaliserInterface[] $normalisers
     */
    public function __construct(private array $normalisers = [])
    {
    }

    public function getNormaliser(mixed $item): NormaliserInterface
    {
        foreach ($this->normalisers as $normaliser) {
            if ($normaliser->supports($item)) {
                return $normaliser;
            }
        }

        throw new NoNormaliserFoundException('No normaliser found');
    }

    public function addNormaliser(NormaliserInterface $normaliser): void
    {
        $this->normalisers[] = $normaliser;
    }
}
