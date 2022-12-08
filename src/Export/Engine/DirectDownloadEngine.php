<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
            $this->getLogger()->info('Starting a direct download export', ['export_filename' => $exportRequest->getFilename()]);

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
            $filename = $exporter->getFilename($exportRequest->getFilename());

            $this->getLogger()->info('Finishing a direct download export', ['export_filename' => $exportRequest->getFilename()]);

            return new DownloadResponse($exportedContent, $filename);
        } catch (\Throwable $e) {
            throw new ExportFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
