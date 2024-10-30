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

use Parthenon\Athena\Export\NormaliserBuilderInterface;
use Parthenon\Athena\Filters\ListFilters;

abstract class AbstractSection implements SectionInterface
{
    public function buildListView(ListView $listView): ListView
    {
        $reflectionObject = new \ReflectionObject($this->getEntity());

        $camelCaseToSnakeCase = function ($input) {
            if (0 === preg_match('/[A-Z]/', $input)) {
                return $input;
            }
            $pattern = '/([a-z])([A-Z])/';

            return strtolower(preg_replace_callback($pattern, function ($a) {
                return $a[1].'_'.strtolower($a[2]);
            }, $input));
        };

        foreach ($reflectionObject->getProperties() as $property) {
            $listView->addField($camelCaseToSnakeCase($property->getName()), 'text');
        }

        return $listView;
    }

    public function preSave($entity): void
    {
    }

    public function postSave($entity): void
    {
    }

    public function buildFilters(ListFilters $listFilters): ListFilters
    {
        return $listFilters;
    }

    public function buildReadView(ReadView $readView): ReadView
    {
        return $readView;
    }

    public function buildEntityForm(EntityForm $entityForm): EntityForm
    {
        return $entityForm;
    }

    public function getRelatedPages(): array
    {
        return [];
    }

    public function getSettings(): Settings
    {
        return new Settings([]);
    }

    public function getAccessRights(): array
    {
        return [];
    }

    public function getButtons(): array
    {
        return [];
    }

    public function buildNormalsier(NormaliserBuilderInterface $builder): NormaliserBuilderInterface
    {
        return $builder;
    }
}
