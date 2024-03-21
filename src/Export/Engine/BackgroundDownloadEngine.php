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
