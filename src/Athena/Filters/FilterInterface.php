<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Filters;

interface FilterInterface
{
    public function getName(): string;

    public function setData($data): self;

    public function setFieldName(string $fieldName): self;

    public function getFieldName(): string;

    public function getHeaderName(): string;

    public function getData();

    public function hasData(): bool;
}
