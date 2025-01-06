<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
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

namespace Parthenon\Export\Engine;

use Parthenon\Export\BackgroundEmailExportRequest;
use Parthenon\Export\ExportRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;
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
