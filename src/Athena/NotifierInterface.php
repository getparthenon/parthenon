<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Entity\Link;

interface NotifierInterface
{
    public function notify(string $message, Link $link): void;
}
