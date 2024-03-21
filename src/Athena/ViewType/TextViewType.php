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

namespace Parthenon\Athena\ViewType;

use Parthenon\Athena\Entity\CrudEntityInterface;

final class TextViewType implements ViewTypeInterface
{
    private $data;

    public function getHtmlOutput(): string
    {
        if ($this->data instanceof \DateTime) {
            return $this->data->format(DATE_ATOM);
        }

        if (is_array($this->data)) {
            return implode(',', $this->data);
        }

        if ($this->data instanceof CrudEntityInterface) {
            return $this->data->getDisplayName();
        }

        return (string) $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getName(): string
    {
        return 'text';
    }
}
