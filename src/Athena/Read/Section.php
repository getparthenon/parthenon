<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
