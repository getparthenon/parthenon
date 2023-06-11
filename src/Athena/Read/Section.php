<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Read;

use Parthenon\Athena\Field;

final class Section
{
    private array $fields = [];

    private string $name;

    private ?string $controller;

    public function __construct(string $name, ?string $controller = null)
    {
        $this->name = $name;
        $this->controller = $controller;
    }

    public function hasController(): bool
    {
        return !empty($this->controller);
    }

    public function getController(): string
    {
        return (string) $this->controller;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function addField(Field $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function getHeaderName(): string
    {
        return ucwords(str_replace('_', ' ', $this->name));
    }
}
