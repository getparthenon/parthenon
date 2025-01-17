<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
