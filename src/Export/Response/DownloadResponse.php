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

namespace Parthenon\Export\Response;

use Parthenon\Export\ExportResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DownloadResponse implements ExportResponseInterface
{
    public function __construct(private string $content, private string $filename)
    {
    }

    public function getSymfonyResponse(): Response
    {
        $response = new Response($this->content);
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $this->filename);

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
