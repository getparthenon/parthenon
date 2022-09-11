<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Filters\ListFilters;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface SectionInterface
{
    public function getUrlTag(): string;

    public function getRepository(): CrudRepositoryInterface;

    public function buildListView(ListView $listView): ListView;

    public function buildFilters(ListFilters $listFilters): ListFilters;

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
}
