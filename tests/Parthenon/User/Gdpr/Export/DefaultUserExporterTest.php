<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
