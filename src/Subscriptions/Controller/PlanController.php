<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Controller;

use Parthenon\Subscriptions\Plan\PlanManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PlanController
{
    #[Route('/plans', name: 'parthenon_subscriptions_plan_list')]
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
