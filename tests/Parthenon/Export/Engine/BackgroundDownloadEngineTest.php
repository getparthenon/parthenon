<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Export\Engine;

use Parthenon\Export\Entity\BackgroundExportRequest;
use Parthenon\Export\ExportRequest;
use Parthenon\Export\Repository\BackgroundExportRequestRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class BackgroundDownloadEngineTest extends TestCase
{
    public function testSendToMessenger()
    {
        $messageBus = $this->createMock(MessageBusInterface::class);
        $requestRepository = $this->createMock(BackgroundExportRequestRepositoryInterface::class);
        $exportRequest = $this->createMock(ExportRequest::class);

        $exportRequest->method('getName')->willReturn('filename');
        $exportRequest->method('getExportFormat')->willReturn('filename');
        $exportRequest->method('getDataProviderService')->willReturn('filename');
        $exportRequest->method('getDataProviderParameters')->willReturn(['parameters']);

        $requestRepository->method('save')->with($this->isInstanceOf(BackgroundExportRequest::class));
        $messageBus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(BackgroundExportRequest::class));

        $subject = new BackgroundDownloadEngine($messageBus, $requestRepository);
        $subject->process($exportRequest);
    }
}
