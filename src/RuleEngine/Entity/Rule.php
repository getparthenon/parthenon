<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Entity;

class Rule
{
    protected int $id;

    protected string $entity;

    protected string $action;

    protected string $comparison;

    protected string $field;

    protected string $value;

    protected ?array $options = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getComparison(): string
    {
        return $this->comparison;
    }

    public function setComparison(string $comparison): self
    {
        $this->comparison = $comparison;

        return $this;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array|string $options
     */
    public function setOptions($options): self
    {
        if (is_string($options)) {
            $options = json_decode($options, true);
        }

        $this->options = $options;

        return $this;
    }
}
