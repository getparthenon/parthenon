<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Health\Controller;

use Parthenon\Health\Checks\CheckManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class HealthcheckController
{
    /**
     * @Route("/health", name="parthenon_healthcheck")
     */
    public function health(CheckManager $checkManager): JsonResponse
    {
        $isHealthy = true;
        $httpStatus = 200;
        $checks = [];
        foreach ($checkManager->getChecks() as $check) {
            $status = $check->getStatus();

            if (false === $status) {
                $isHealthy = false;
                $httpStatus = 500;
            }
            $checks[$check->getName()] = $status;
        }

        return new JsonResponse([
            'isHealthy' => $isHealthy,
            'checks' => $checks,
        ], $httpStatus);
    }
}
