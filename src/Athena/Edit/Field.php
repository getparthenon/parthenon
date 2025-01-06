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

namespace Parthenon\Athena\Edit;

final class Field
{
    public function __construct(
        private string $name,
        private string $type,
        private array $extraOptions = [],
        private bool $editable = true)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getExtraOptions(): array
    {
        return $this->extraOptions;
    }

    public function hasSubEntity(): bool
    {
        return str_contains($this->name, '.');
    }

    public function getSubName(): string
    {
        [$part, $name] = explode('.', $this->name, 2);

        return $part;
    }

    public function getSubField(): Field
    {
        [$part, $name] = explode('.', $this->name, 2);

        return new static($name, $this->type, $this->extraOptions);
    }

    public function isEditable(): bool
    {
        return $this->editable;
    }
}
