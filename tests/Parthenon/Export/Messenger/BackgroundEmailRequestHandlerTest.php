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

namespace Parthenon\Export\Messenger;

use Parthenon\Export\BackgroundEmailExportRequest;
use Parthenon\Export\DataProvider\DataProviderFetcherInterface;
use Parthenon\Export\DataProvider\DataProviderInterface;
use Parthenon\Export\Exporter\ExporterInterface;
use Parthenon\Export\Exporter\ExporterManagerInterface;
use Parthenon\Export\Normaliser\NormaliserInterface;
use Parthenon\Export\Normaliser\NormaliserManagerInterface;
use Parthenon\Export\Notification\ExportEmailFactoryInterface;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\Email;
use Parthenon\Notification\EmailSenderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class BackgroundEmailRequestHandlerTest extends TestCase
{
    public function testBackgroundEmail()
    {
        $objectOne = new \stdClass();
        $objectTwo = new \stdClass();
        $data = [$objectOne, $objectTwo];
        $rowOne = [5, 6];
        $rowTwo = [7, 8];
        $expectedOutput = [$rowOne, $rowTwo];
        $exportOutput = 'output';
        $backgroundEmailExport = $this->createMock(BackgroundEmailExportRequest::class);
        $dataProviderFetcher = $this->createMock(DataProviderFetcherInterface::class);
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $exportManagerInterface = $this->createMock(ExporterManagerInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);
        $normaliserManager = $this->createMock(NormaliserManagerInterface::class);
        $normaliser = $this->createMock(NormaliserInterface::class);
        $userProvider = $this->createMock(UserProviderInterface::class);
        $emailSender = $this->createMock(EmailSenderInterface::class);
        $exportEmailFactory = $this->createMock(ExportEmailFactoryInterface::class);
        $email = $this->createMock(Email::class);
        $user = $this->createMock(UserInterface::class);

        $backgroundEmailExport->method('getUser')->willReturn($user);

        $dataProviderFetcher->method('getDataProvider')->willReturn($dataProvider);
        $dataProvider->method('getData')->with($backgroundEmailExport)->willReturn($data);

        $normaliserManager->expects($this->once())->method('getNormaliser')->with($objectOne)->willReturn($normaliser);
        $normaliser->method('normalise')->willReturn($rowOne, $rowTwo);

        $exportManagerInterface->method('getExporter')->willReturn($exporter);
        $exporter->method('getOutput')->with($expectedOutput)->willReturn($exportOutput);

        $exportEmailFactory->method('buildEmail')->with($backgroundEmailExport)->willReturn($email);

        $userProvider->method('refreshUser')->willReturn($user);

        $email->method('addAttachment')->with($this->isInstanceOf(Attachment::class));
        $emailSender->expects($this->once())->method('send')->with($email);

        $subject = new BackgroundEmailRequestHandler($dataProviderFetcher, $exportManagerInterface, $normaliserManager, $userProvider, $emailSender, $exportEmailFactory);
        $subject->__invoke($backgroundEmailExport);
    }
}
