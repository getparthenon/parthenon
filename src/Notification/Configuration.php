<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification;

final class Configuration
{
    private string $fromAddress;

    private string $fromName;

    /**
     * Configuration constructor.
     */
    public function __construct(string $fromName, string $fromAddress)
    {
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
    }

    public function getFromAddress(): string
    {
        return $this->fromAddress;
    }

    public function getFromName(): string
    {
        return $this->fromName;
    }
}
