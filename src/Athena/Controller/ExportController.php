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

namespace Parthenon\Athena\Controller;

use Parthenon\Export\Entity\BackgroundExportRequest;
use Parthenon\Export\Repository\BackgroundExportRequestRepositoryInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ExportController
{
    #[Template('@Parthenon/athena/crud/export_background_email.html.twig')]
    public function emailWaiting(LoggerInterface $logger)
    {
        $logger->info("An Athena user has been told they'll receive an email");

        return [];
    }

    public function downloadWaiting(Request $request, LoggerInterface $logger, BackgroundExportRequestRepositoryInterface $backgroundExportRequestRepository, Environment $twig)
    {
        $logger->info('An Athena user has checked the process of an export');

        $id = $request->get('id');

        /** @var BackgroundExportRequest $backgroundExportRequest */
        $backgroundExportRequest = $backgroundExportRequestRepository->findById($id);

        $downloadUrl = $backgroundExportRequest->getExportedFilePath();

        if ($downloadUrl) {
            return new RedirectResponse($downloadUrl);
        } else {
            return new Response($twig->render('@Parthenon/athena/crud/export_background_download.html.twig'));
        }
    }
}
