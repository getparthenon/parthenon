<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
