<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification\Slack;

interface ConfigProviderInterface
{
    public function getAppData(string $code): array;
}
