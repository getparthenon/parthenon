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

namespace Parthenon\Export\Engine;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Export\Entity\BackgroundExportRequest;
use Parthenon\Export\Exception\ExportFailedException;
use Parthenon\Export\ExportRequest;
use Parthenon\Export\ExportResponseInterface;
use Parthenon\Export\Repository\BackgroundExportRequestRepositoryInterface;
use Parthenon\Export\Response\WaitingResponse;
use Symfony\Component\Messenger\MessageBusInterface;

final class BackgroundDownloadEngine implements EngineInterface
{
    use LoggerAwareTrait;

    public const NAME = 'background_download';

    public function __construct(private MessageBusInterface $messengerBus, private BackgroundExportRequestRepositoryInterface $backgroundExportRequestRepository)
    {
    }

    public function process(ExportRequest $exportRequest): ExportResponseInterface
    {
        try {
            $this->getLogger()->info('Queuing a background download export', ['export_filename' => $exportRequest->getName()]);

            $backgroundExportRequest = BackgroundExportRequest::createFromExportRequest($exportRequest);

            $this->backgroundExportRequestRepository->save($backgroundExportRequest);
            $this->messengerBus->dispatch($backgroundExportRequest);

            return new WaitingResponse((string) $backgroundExportRequest->getId());
        } catch (\Throwable $e) {
            throw new ExportFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
