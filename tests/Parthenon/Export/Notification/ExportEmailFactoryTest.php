<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Export\Notification;

use Parthenon\Export\BackgroundEmailExportRequest;
use Parthenon\Export\ExportRequest;
use Parthenon\Notification\Email;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class ExportEmailFactoryTest extends TestCase
{
    public function testEmail()
    {
        $user = new class() implements UserInterface {
            public function getRoles(): array
            {
                return [];
            }

            public function eraseCredentials(): void
            {
                // TODO: Implement eraseCredentials() method.
            }

            public function getUserIdentifier(): string
            {
                return 'iain.cambridge@example.org';
            }

            public function getEmail(): string
            {
                return 'iain.cambridge@example.org';
            }
        };

        $exportRequest = new ExportRequest('filename', 'csv', 'service', []);

        $backgroundEmailexport = BackgroundEmailExportRequest::createFromExportRequest($exportRequest, $user);

        $subject = new ExportEmailFactory();
        $this->assertInstanceOf(Email::class, $subject->buildEmail($backgroundEmailexport));
    }
}
