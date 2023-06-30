<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\ViewType;

use Parthenon\Athena\Entity\CrudEntityInterface;

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

        if ($this->data instanceof CrudEntityInterface) {
            return $this->data->getDisplayName();
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
