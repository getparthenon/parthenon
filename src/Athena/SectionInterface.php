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

use Parthenon\Athena\Export\NormaliserBuilderInterface;
use Parthenon\Athena\Filters\ListFilters;
use Parthenon\Athena\Filters\ListFiltersInterface;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface SectionInterface
{
    public function getUrlTag(): string;

    public function getRepository(): CrudRepositoryInterface;

    public function buildListView(ListView $listView): ListView;

    public function buildFilters(ListFilters $listFilters): ListFiltersInterface;

    public function buildReadView(ReadView $readView): ReadView;

    public function buildEntityForm(EntityForm $entityForm): EntityForm;

    public function getEntity();

    public function getMenuSection(): string;

    public function getMenuName(): string;

    public function getRelatedPages(): array;

    public function getSettings(): Settings;

    public function getButtons(): array;

    public function getAccessRights(): array;

    public function preSave($entity): void;

    public function postSave($entity): void;

    public function buildNormalsier(NormaliserBuilderInterface $normaliserBuilder): NormaliserBuilderInterface;
}
