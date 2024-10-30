<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

namespace Parthenon\Athena;

use Parthenon\Athena\ViewType\ViewTypeInterface;
use Parthenon\Common\FieldAccesorTrait;

final class Field
{
    use FieldAccesorTrait;

    private string $name;
    private ViewTypeInterface $viewType;
    private bool $sortable;
    private bool $link;

    public function __construct(string $name, ViewTypeInterface $viewType, $sortable = false, bool $link = false)
    {
        $this->name = $name;
        $this->viewType = $viewType;
        $this->sortable = $sortable;
        $this->link = $link;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOutput($item)
    {
        $this->viewType->setData($this->getFieldData($item, $this->name));

        return $this->viewType->getHtmlOutput();
    }

    public function getViewType(): ViewTypeInterface
    {
        return $this->viewType;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isLink(): bool
    {
        return $this->link;
    }

    public function getHeaderName(): string
    {
        $headerName = str_replace('.', ' ', $this->name);

        return ucwords(str_replace('_', ' ', $headerName));
    }
}
