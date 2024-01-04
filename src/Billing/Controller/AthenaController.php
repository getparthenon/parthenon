<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
