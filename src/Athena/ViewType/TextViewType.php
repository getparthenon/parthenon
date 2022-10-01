<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
