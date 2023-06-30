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
