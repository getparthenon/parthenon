<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification;

use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testReturnsValues()
    {
        $fromName = 'from name';
        $fromAddress = 'from@example.org';

        $configuration = new Configuration($fromName, $fromAddress);

        $this->assertEquals($fromName, $configuration->getFromName());
        $this->assertEquals($fromAddress, $configuration->getFromAddress());
    }
}
