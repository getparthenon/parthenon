<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
