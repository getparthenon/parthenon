<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
