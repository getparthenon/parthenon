<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
