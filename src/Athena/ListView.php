<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

use Parthenon\Common\FieldAccesorTrait;

final class ListView implements ListViewInterface
{
    use FieldAccesorTrait;

    /**
     * @var Field[]
     */
    protected array $fields = [];

    private ViewTypeManagerInterface $viewTypeManager;

    public function __construct(ViewTypeManagerInterface $viewTypeManager)
    {
        $this->viewTypeManager = $viewTypeManager;
    }

    public function addField(string $fieldName, string $fieldType, bool $sortable = false, bool $link = false): self
    {
        $this->fields[$fieldName] = new Field($fieldName, $this->viewTypeManager->get($fieldType), $sortable, $link);

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getHeaders(): array
    {
        $output = [];
        foreach ($this->fields as $field) {
            $output[] = $this->convertToHeader($field->getName());
        }

        return $output;
    }

    public function isLink($name)
    {
        return $this->fields[$name]->isLink();
    }

    public function getData($item): array
    {
        $output = [];
        foreach ($this->fields as $field) {
            $type = $field->getViewType();
            $type->setData($this->getFieldData($item, $field->getName()));
            $output[$field->getName()] = $type;
        }

        return $output;
    }

    private function convertToHeader(string $name): string
    {
        $parts = explode('.', $name);
        $name = end($parts);

        return ucwords(str_replace('_', ' ', $name));
    }
}
