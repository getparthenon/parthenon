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

namespace Parthenon\Export\Engine;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Export\DataProvider\DataProviderFetcherInterface;
use Parthenon\Export\Exception\ExportFailedException;
use Parthenon\Export\Exporter\ExporterManagerInterface;
use Parthenon\Export\ExportRequest;
use Parthenon\Export\ExportResponseInterface;
use Parthenon\Export\Normaliser\NormaliserManagerInterface;
use Parthenon\Export\Response\DownloadResponse;

final class DirectDownloadEngine implements EngineInterface
{
    use LoggerAwareTrait;

    public const NAME = 'direct_download';

    public function __construct(
        private NormaliserManagerInterface $normaliserManager,
        private ExporterManagerInterface $exporterManager,
        private DataProviderFetcherInterface $dataProviderFetcher,
    ) {
    }

    public function process(ExportRequest $exportRequest): ExportResponseInterface
    {
        try {
            $this->getLogger()->info('Starting a direct download export', ['export_filename' => $exportRequest->getName()]);

            $data = $this->dataProviderFetcher->getDataProvider($exportRequest)->getData($exportRequest);

            $exporter = $this->exporterManager->getExporter($exportRequest);

            $normaliser = null;
            $normalisedData = [];

            foreach ($data as $item) {
                // Done this way incase it's a generator.
                if (!isset($normaliser)) {
                    $normaliser = $this->normaliserManager->getNormaliser($item);
                }

                $normalisedData[] = $normaliser->normalise($item);
            }

            $exportedContent = $exporter->getOutput($normalisedData);
            $filename = $exporter->getFilename($exportRequest->getName());

            $this->getLogger()->info('Finishing a direct download export', ['export_filename' => $exportRequest->getName()]);

            return new DownloadResponse($exportedContent, $filename);
        } catch (\Throwable $e) {
            throw new ExportFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
