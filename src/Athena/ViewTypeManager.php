<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Exception\InvalidViewTypeException;
use Parthenon\Athena\ViewType\ViewTypeInterface;

final class ViewTypeManager implements ViewTypeManagerInterface
{
    /***
     * @var ViewTypeInterface[]
     */
    private $viewTypes = [];

    public function add(ViewTypeInterface $viewType): self
    {
        $this->viewTypes[] = $viewType;

        return $this;
    }

    public function get(string $typeName): ViewTypeInterface
    {
        foreach ($this->viewTypes as $viewType) {
            if ($viewType->getName() === $typeName) {
                return clone $viewType;
            }
        }

        throw new InvalidViewTypeException('The view type '.$typeName.' is invalid');
    }
}
