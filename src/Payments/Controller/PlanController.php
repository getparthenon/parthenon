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

namespace Parthenon\Payments\Controller;

use Parthenon\Payments\Plan\PlanManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PlanController
{
    #[Route('/plans', name: 'parthenon_payments_plan_list')]
    public function listAction(PlanManager $planManager)
    {
        $plans = $planManager->getPlans();

        $output = [];

        foreach ($plans as $plan) {
            $output[$plan->getName()] = [
                'name' => $plan->getName(),
                'limits' => $plan->getLimits(),
            ];
        }

        return new JsonResponse(['plans' => $output]);
    }
}
