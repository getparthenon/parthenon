<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Controller;

use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PlanController
{
    use LoggerAwareTrait;

    #[Route('/billing/plans', name: 'parthenon_billing_plan_list')]
    public function listAction(PlanManagerInterface $planManager, CustomerProviderInterface $customerProvider)
    {
        $this->getLogger()->info('Getting plans info');
        $plans = $planManager->getPlans();

        $output = [];
        $currentPlanOutput = [];
        try {
            $currentPlanOutput =
                [
                    'plan_name' => $customerProvider->getCurrentCustomer()->getSubscription()->getPlanName(),
                    'status' => $customerProvider->getCurrentCustomer()->getSubscription()->getStatus(),
                    'payment_schedule' => $customerProvider->getCurrentCustomer()->getSubscription()->getPaymentSchedule(),
                ];
        } catch (NoCustomerException $exception) {
            $this->getLogger()->error('No customer found');

            return new JsonResponse([], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        foreach ($plans as $plan) {
            $output[$plan->getName()] = [
                'name' => $plan->getName(),
                'limits' => $plan->getLimits(),
                'features' => $plan->getFeatures(),
                'prices' => $this->generateSchedules($plan),
            ];
        }

        return new JsonResponse([
            'plans' => $output,
            'current_plan' => $currentPlanOutput,
            ]);
    }

    private function generateSchedules(Plan $plan): array
    {
        $output = [];

        foreach ($plan->getPublicPrices() as $data) {
            $output[$data->getSchedule()][$data->getCurrency()] = [
                'schedule' => $data->getSchedule(),
                'amount' => $data->getPriceAsMoney()->getAmount(),
                'currency' => $data->getCurrency(),
            ];
        }

        return $output;
    }
}
