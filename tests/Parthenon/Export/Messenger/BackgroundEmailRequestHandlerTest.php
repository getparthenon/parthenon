<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
