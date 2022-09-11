<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
