<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\ViewType\ViewTypeInterface;

interface ViewTypeManagerInterface
{
    public function add(ViewTypeInterface $viewType): ViewTypeManager;

    public function get(string $typeName): ViewTypeInterface;
}
