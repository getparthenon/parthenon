<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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
use Parthenon\Export\BackgroundEmailExportRequest;
use Parthenon\Export\Exception\ExportFailedException;
use Parthenon\Export\ExportRequest;
use Parthenon\Export\ExportResponseInterface;
use Parthenon\Export\Response\EmailResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;

final class BackgroundEmailEngine implements EngineInterface
{
    use LoggerAwareTrait;

    public const NAME = 'background_email';

    public function __construct(private Security $security, private MessageBusInterface $messengerBus)
    {
    }

    public function process(ExportRequest $exportRequest): ExportResponseInterface
    {
        try {
            $this->getLogger()->info('Queuing a background email export', ['export_filename' => $exportRequest->getName()]);

            $backgroundEmail = BackgroundEmailExportRequest::createFromExportRequest($exportRequest, $this->security->getUser());
            $this->messengerBus->dispatch($backgroundEmail);

            return new EmailResponse();
        } catch (\Throwable $e) {
            throw new ExportFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
