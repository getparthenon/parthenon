<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\ViewType;

final class BooleanViewType implements ViewTypeInterface
{
    private $data;

    public function getHtmlOutput(): string
    {
        return $this->data ? 'True' : 'False';
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getName(): string
    {
        return 'boolean';
    }
}
