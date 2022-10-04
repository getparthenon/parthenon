<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena;

interface ListViewInterface
{
    public function addField(string $fieldName, string $fieldType, bool $sortable = false, bool $link = false): ListViewInterface;

    public function getFields(): array;

    public function getHeaders(): array;

    public function isLink($name);

    public function getData($item): array;
}
