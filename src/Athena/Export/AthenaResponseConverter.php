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
