<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
