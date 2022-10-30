<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Exception\InvalidViewTypeException;
use Parthenon\Athena\ViewType\ViewTypeInterface;

final class ViewTypeManager implements ViewTypeManagerInterface
{
    /***
     * @var ViewTypeInterface[]
     */
    private $viewTypes = [];

    public function add(ViewTypeInterface $viewType): self
    {
        $this->viewTypes[] = $viewType;

        return $this;
    }

    public function get(string $typeName): ViewTypeInterface
    {
        foreach ($this->viewTypes as $viewType) {
            if ($viewType->getName() === $typeName) {
                return clone $viewType;
            }
        }

        throw new InvalidViewTypeException('The view type '.$typeName.' is invalid');
    }
}
