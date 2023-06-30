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

            public function eraseCredentials()
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
