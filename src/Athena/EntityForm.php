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

namespace Parthenon\Athena;

use Parthenon\Athena\Edit\Field as EditField;
use Parthenon\Athena\Edit\Section;
use Parthenon\Athena\Exception\NoSectionOpenException;
use Parthenon\Athena\Exception\SectionAlreadyOpenException;

final class EntityForm
{
    /**
     * @var Section[]
     */
    private array $sections = [];

    private ?Section $openSection = null;

    public function section(string $name): self
    {
        if (!is_null($this->openSection)) {
            throw new SectionAlreadyOpenException();
        }

        $this->sections[] = $this->openSection = new Section($name);

        return $this;
    }

    public function field(string $name, $type = 'text', array $extraOptions = [], bool $editable = true): self
    {
        if (is_null($this->openSection)) {
            throw new NoSectionOpenException();
        }

        $this->openSection->addField(new EditField($name, $type, $extraOptions, $editable));

        return $this;
    }

    public function end(): self
    {
        $this->openSection = null;

        return $this;
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    // TODO consider yield

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        $fields = [];

        foreach ($this->sections as $section) {
            $fields[] = $section->getFields();
        }

        return array_merge([], ...$fields);
    }
}
