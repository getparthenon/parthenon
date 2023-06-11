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

namespace Parthenon\Athena;

use Parthenon\Athena\ViewType\ViewTypeInterface;
use Parthenon\Common\FieldAccesorTrait;

final class Field
{
    use FieldAccesorTrait;

    private string $name;
    private ViewTypeInterface $viewType;
    private bool $sortable;
    private bool $link;

    public function __construct(string $name, ViewTypeInterface $viewType, $sortable = false, bool $link = false)
    {
        $this->name = $name;
        $this->viewType = $viewType;
        $this->sortable = $sortable;
        $this->link = $link;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOutput($item)
    {
        $this->viewType->setData($this->getFieldData($item, $this->name));

        return $this->viewType->getHtmlOutput();
    }

    public function getViewType(): ViewTypeInterface
    {
        return $this->viewType;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isLink(): bool
    {
        return $this->link;
    }

    public function getHeaderName(): string
    {
        $headerName = str_replace('.', ' ', $this->name);

        return ucwords(str_replace('_', ' ', $headerName));
    }
}
