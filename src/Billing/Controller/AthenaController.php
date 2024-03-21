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

namespace Parthenon\Billing\Controller;

use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Refund\RefundManagerInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AthenaController
{
    #[Route('/athena/billing/subscription/{id}/cancel', name: 'parthenon_billing_athena_subscription_cancel', methods: ['GET'])]
    public function cancelSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionManagerInterface $subscriptionManager,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new RedirectResponse($urlGenerator->generate('parthenon_athena_crud_subscriptions_list'));
        }
        $subscriptionManager->cancelSubscriptionAtEndOfCurrentPeriod($subscription);
        $subscriptionRepository->save($subscription);
        $request->getSession()->getFlashBag()->add('success', 'Cancelled');

        return new RedirectResponse($urlGenerator->generate('parthenon_athena_crud_subscriptions_read', ['id' => $request->get('id')]));
    }

    #[Route('/athena/billing/subscription/{id}/cancel-refund', name: 'parthenon_billing_athena_subscription_cancel_refund', methods: ['GET'])]
    public function cancelAndRefundSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionManagerInterface $subscriptionManager,
        RefundManagerInterface $refundManager,
        UrlGeneratorInterface $urlGenerator,
        Security $security,
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new RedirectResponse($urlGenerator->generate('parthenon_athena_crud_subscriptions_list'));
        }

        $subscriptionManager->cancelSubscriptionInstantly($subscription);
        $subscriptionRepository->save($subscription);

        $refundManager->issueFullRefundForSubscription($subscription, $security->getUser());

        $request->getSession()->getFlashBag()->add('success', 'Cancelled and refunded');

        return new RedirectResponse($urlGenerator->generate('parthenon_athena_crud_subscriptions_read', ['id' => $request->get('id')]));
    }
}
