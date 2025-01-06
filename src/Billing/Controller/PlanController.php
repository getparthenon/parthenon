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

namespace Parthenon\Billing\Controller;

use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Subscription\SubscriptionProviderInterface;
use Parthenon\Common\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class PlanController
{
    use LoggerAwareTrait;

    #[Route('/billing/plans', name: 'parthenon_billing_plan_list_h')]
    public function listAction(
        PlanManagerInterface $planManager,
        CustomerProviderInterface $customerProvider,
        SubscriptionProviderInterface $subscriptionProvider,
        LoggerInterface $logger,
    ) {
        $this->getLogger()->info('Getting plans info');
        $plans = $planManager->getPlans();

        $output = [];
        $currentPlanOutput = [];
        try {
            $currentCustomer = $customerProvider->getCurrentCustomer();
        } catch (NoCustomerException $exception) {
            $this->getLogger()->error('No customer found');

            return new JsonResponse([], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $subscriptions = $subscriptionProvider->getSubscriptionsForCustomer($currentCustomer);
        foreach ($subscriptions as $subscription) {
            $currentPlanOutput[] = [
                'name' => $subscription->getPlanName(),
                'schedule' => $subscription->getPaymentSchedule(),
                'id' => (string) $subscription->getId(),
                'currency' => $subscription->getCurrency(),
            ];
        }

        foreach ($plans as $plan) {
            if (!$plan->isPublic()) {
                continue;
            }
            $output[$plan->getName()] = [
                'name' => $plan->getName(),
                'limits' => $plan->getLimits(),
                'features' => $plan->getFeatures(),
                'prices' => $this->generateSchedules($plan),
            ];
        }

        return new JsonResponse([
            'plans' => $output,
            'current_plans' => $currentPlanOutput,
        ]);
    }

    private function generateSchedules(Plan $plan): array
    {
        $output = [];

        foreach ($plan->getPublicPrices() as $data) {
            $output[$data->getSchedule()][strtoupper($data->getCurrency())] = [
                'schedule' => $data->getSchedule(),
                'amount' => $data->getAsMoney()->getAmount(),
                'currency' => strtoupper($data->getCurrency()),
            ];
        }

        return $output;
    }
}
