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

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Export\BackgroundEmailExportRequest;
use Parthenon\Export\DataProvider\DataProviderFetcherInterface;
use Parthenon\Export\Exporter\ExporterManagerInterface;
use Parthenon\Export\Normaliser\NormaliserManagerInterface;
use Parthenon\Export\Notification\ExportEmailFactoryInterface;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\User\UserProviderInterface;

#[AsMessageHandler]
final class BackgroundEmailRequestHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private DataProviderFetcherInterface $dataProviderFetcher,
        private ExporterManagerInterface $exporterManager,
        private NormaliserManagerInterface $normaliserManager,
        private UserProviderInterface $userProvider,
        private EmailSenderInterface $emailSender,
        private ExportEmailFactoryInterface $emailFactory,
    ) {
    }

    public function __invoke(BackgroundEmailExportRequest $message)
    {
        $this->getLogger()->info('Processing background email export request', ['export_filename' => $message->getName()]);

        $user = $this->userProvider->refreshUser($message->getUser());
        $message->setUser($user);

        $dataProvider = $this->dataProviderFetcher->getDataProvider($message);

        $exporter = $this->exporterManager->getExporter($message);
        $data = $dataProvider->getData($message);
        $normaliser = null;
        $normalisedData = [];

        foreach ($data as $item) {
            // Done this way in case it's a generator.
            if (!isset($normaliser)) {
                $normaliser = $this->normaliserManager->getNormaliser($item);
            }

            $normalisedData[] = $normaliser->normalise($item);
        }

        $exportedContent = $exporter->getOutput($normalisedData);
        $filename = $exporter->getFilename($message->getName());

        $email = $this->emailFactory->buildEmail($message);

        $attachment = new Attachment($filename, $exportedContent);
        $email->addAttachment($attachment);

        $this->emailSender->send($email);

        $this->getLogger()->info('Finished processing background email export request', ['export_filename' => $message->getName()]);
    }
}
