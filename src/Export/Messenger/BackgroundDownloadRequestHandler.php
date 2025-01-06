<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\Export\Messenger;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\Upload\UploaderInterface;
use Parthenon\Export\DataProvider\DataProviderFetcherInterface;
use Parthenon\Export\Entity\BackgroundExportRequest;
use Parthenon\Export\Exporter\ExporterManagerInterface;
use Parthenon\Export\Normaliser\NormaliserManagerInterface;
use Parthenon\Export\Repository\BackgroundExportRequestRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class BackgroundDownloadRequestHandler
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
