<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class BackgroundEmailRequestHandler implements MessageHandlerInterface
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
