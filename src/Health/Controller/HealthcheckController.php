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
