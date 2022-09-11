<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
