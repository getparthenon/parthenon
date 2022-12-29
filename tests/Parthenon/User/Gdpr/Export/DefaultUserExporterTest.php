<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class DefaultUserExporterTest extends TestCase
{
    public function testReturnsData()
    {
        $user = new User();
        $user->setName('Iain Cambridge');
        $user->setEmail('iain@example.org');

        $exporter = new DefaultUserExporter();
        $output = $exporter->export($user);

        $this->assertEquals(['name' => 'Iain Cambridge', 'email' => 'iain@example.org'], $output);
    }
}
