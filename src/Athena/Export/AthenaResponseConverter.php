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

namespace Parthenon\Athena\Export;

use Parthenon\Export\Exception\UnsupportedResponseTypeException;
use Parthenon\Export\ExportResponseInterface;
use Parthenon\Export\Response\DownloadResponse;
use Parthenon\Export\Response\EmailResponse;
use Parthenon\Export\Response\ResponseConverterInterface;
use Parthenon\Export\Response\WaitingResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AthenaResponseConverter implements ResponseConverterInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function convert(ExportResponseInterface $exportResponse): Response
    {
        if ($exportResponse instanceof DownloadResponse) {
            return $exportResponse->getSymfonyResponse();
        } elseif ($exportResponse instanceof EmailResponse) {
            return new RedirectResponse($this->urlGenerator->generate('parthenon_athena_export_email'));
        } elseif ($exportResponse instanceof WaitingResponse) {
            return new RedirectResponse($this->urlGenerator->generate('parthenon_athena_export_download', ['id' => $exportResponse->getId()]));
        }

        throw new UnsupportedResponseTypeException(sprintf("The response type '%s' is not supported", get_class($exportResponse)));
    }
}
