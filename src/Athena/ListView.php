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

use Parthenon\Common\FieldAccesorTrait;

final class ListView implements ListViewInterface
{
    use FieldAccesorTrait;

    /**
     * @var Field[]
     */
    protected array $fields = [];

    private ViewTypeManagerInterface $viewTypeManager;

    public function __construct(ViewTypeManagerInterface $viewTypeManager)
    {
        $this->viewTypeManager = $viewTypeManager;
    }

    public function addField(string $fieldName, string $fieldType, bool $sortable = false, bool $link = false): self
    {
        $this->fields[$fieldName] = new Field($fieldName, $this->viewTypeManager->get($fieldType), $sortable, $link);

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getHeaders(): array
    {
        $output = [];
        foreach ($this->fields as $field) {
            $output[] = $this->convertToHeader($field->getName());
        }

        return $output;
    }

    public function isLink($name)
    {
        return $this->fields[$name]->isLink();
    }

    public function getData($item): array
    {
        $output = [];
        foreach ($this->fields as $field) {
            $type = $field->getViewType();
            $type->setData($this->getFieldData($item, $field->getName()));
            $output[$field->getName()] = $type;
        }

        return $output;
    }

    private function convertToHeader(string $name): string
    {
        $parts = explode('.', $name);
        $name = end($parts);

        return ucwords(str_replace('_', ' ', $name));
    }
}
