<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\ViewType;

final class TextViewType implements ViewTypeInterface
{
    private $data;

    public function getHtmlOutput(): string
    {
        if ($this->data instanceof \DateTime) {
            return $this->data->format(DATE_ATOM);
        }

        if (is_array($this->data)) {
            return implode(',', $this->data);
        }

        return (string) $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getName(): string
    {
        return 'text';
    }
}
