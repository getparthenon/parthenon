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

namespace Parthenon\Export\Notification;

use Parthenon\Export\BackgroundEmailExportRequest;
use Parthenon\Export\ExportRequest;
use Parthenon\Notification\Email;
use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class ExportEmailFactoryTest extends TestCase
{
    public function testEmail()
    {
        $user = new User();
        $user->setEmail('iain.cambridge@example.org');

        $exportRequest = new ExportRequest('filename', 'csv', 'service', []);

        $backgroundEmailexport = BackgroundEmailExportRequest::createFromExportRequest($exportRequest, $user);

        $subject = new ExportEmailFactory();
        $this->assertInstanceOf(Email::class, $subject->buildEmail($backgroundEmailexport));
    }
}
