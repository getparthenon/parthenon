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

namespace Parthenon\Athena\Controller;

use Parthenon\Export\Entity\BackgroundExportRequest;
use Parthenon\Export\Repository\BackgroundExportRequestRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
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
