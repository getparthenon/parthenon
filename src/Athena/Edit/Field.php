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

namespace Parthenon\Athena\Edit;

final class Field
{
    private string $name;
    private string $type;
    private array $extraOptions;

    public function __construct(string $name, string $type, array $extraOptions = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->extraOptions = $extraOptions;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getExtraOptions(): array
    {
        return $this->extraOptions;
    }

    public function hasSubEntity(): bool
    {
        return str_contains($this->name, '.');
    }

    public function getSubName(): string
    {
        [$part, $name] = explode('.', $this->name, 2);

        return $part;
    }

    public function getSubField(): Field
    {
        [$part, $name] = explode('.', $this->name, 2);

        return new static($name, $this->type, $this->extraOptions);
    }
}
