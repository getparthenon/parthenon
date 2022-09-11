<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Logging\Monolog;

final class ProcessIdGenerator
{
    private string $processId;

    public function __construct()
    {
        $this->processId = \sha1(\microtime());
    }

    public function getProcessId(): string
    {
        return $this->processId;
    }
}
