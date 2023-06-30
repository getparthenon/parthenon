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

namespace Parthenon\Export\Messenger;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\Upload\UploaderInterface;
use Parthenon\Export\DataProvider\DataProviderFetcherInterface;
use Parthenon\Export\Entity\BackgroundExportRequest;
use Parthenon\Export\Exporter\ExporterManagerInterface;
use Parthenon\Export\Normaliser\NormaliserManagerInterface;
use Parthenon\Export\Repository\BackgroundExportRequestRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BackgroundDownloadRequestHandler implements MessageHandlerInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private BackgroundExportRequestRepositoryInterface $backgroundExportRequestRepository,
        private DataProviderFetcherInterface $dataProviderFetcher,
        private ExporterManagerInterface $exporterManager,
        private NormaliserManagerInterface $normaliserManager,
        private UploaderInterface $uploader,
    ) {
    }

    public function __invoke(BackgroundExportRequest $message)
    {
        $this->getLogger()->info('Processing background download export request', ['export_filename' => $message->getName()]);

        /** @var BackgroundExportRequest $backgroundExportRequest */
        $backgroundExportRequest = $this->backgroundExportRequestRepository->findById($message->getId());

        $dataProvider = $this->dataProviderFetcher->getDataProvider($backgroundExportRequest);

        $exporter = $this->exporterManager->getExporter($backgroundExportRequest);
        $data = $dataProvider->getData($backgroundExportRequest);
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
        $filename = $exporter->getFilename($backgroundExportRequest->getName());

        $file = $this->uploader->uploadString($filename, $exportedContent);
        $backgroundExportRequest->setExportedFilePath($file->getPath());
        $backgroundExportRequest->setExportedFile($file->getFilename());
        $backgroundExportRequest->setUpdatedAt(new \DateTime());

        $this->backgroundExportRequestRepository->save($backgroundExportRequest);
        $this->getLogger()->info('Finished processing background download export request', ['export_filename' => $message->getName()]);
    }
}
