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
