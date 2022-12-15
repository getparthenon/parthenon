<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Export\Engine;

use Parthenon\Export\BackgroundEmailExportRequest;
use Parthenon\Export\ExportRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class BackgroundEmailEngineTest extends TestCase
{
    public function testSendToMessenger()
    {
        $messageBus = $this->createMock(MessageBusInterface::class);
        $security = $this->createMock(Security::class);
        $user = $this->createMock(UserInterface::class);
        $exportRequest = $this->createMock(ExportRequest::class);

        $exportRequest->method('getName')->willReturn('filename');
        $exportRequest->method('getExportFormat')->willReturn('filename');
        $exportRequest->method('getDataProviderService')->willReturn('filename');
        $exportRequest->method('getDataProviderParameters')->willReturn(['parameters']);

        $security->method('getUser')->willReturn($user);
        $messageBus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(BackgroundEmailExportRequest::class));

        $subject = new BackgroundEmailEngine($security, $messageBus);
        $subject->process($exportRequest);
    }
}
