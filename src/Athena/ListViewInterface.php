<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
