<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
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

namespace Parthenon\Payments\Controller;

use Parthenon\Payments\CheckoutManagerInterface;
use Parthenon\Payments\ConfigInterface;
use Parthenon\Payments\Event\PaymentSuccessEvent;
use Parthenon\Payments\Plan\PlanManager;
use Parthenon\Payments\Plan\PlanManagerInterface;
use Parthenon\Payments\PriceProviderInterface;
use Parthenon\Payments\Repository\SubscriberRepositoryInterface;
use Parthenon\Payments\Subscriber\CurrentSubscriberProviderInterface;
use Parthenon\Payments\Subscriber\SubscriptionFactoryInterface;
use Parthenon\Payments\SubscriptionManagerInterface;
use Parthenon\Payments\SubscriptionOptionsFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentsController
{
    #[Route('/payments/plans/checkout/{planName}/{paymentSchedule}', name: 'parthenon_payment_checkout')]
    public function createCheckout(
        Request $request,
        LoggerInterface $logger,
        CurrentSubscriberProviderInterface $subscriberProvider,
        SubscriberRepositoryInterface $subscriberRepository,
        PlanManagerInterface $planManager,
        SubscriptionFactoryInterface $subscriptionFactory,
        SubscriptionOptionsFactoryInterface $subscriptionOptionsFactory,
        CheckoutManagerInterface $checkoutManager,
    ) {
        $content = json_decode($request->getContent(), true);
        $seats = 1;
        if ($content) {
            if (isset($content['seats'])) {
                $seats = (int) $content['seats'];
            }
        }

        $planName = $request->get('planName');
        $paymentSchedule = $request->get('paymentSchedule');

        $logger->info('Generate checkout session', ['planName' => $planName, 'paymentSchedule' => $paymentSchedule]);

        $plan = $planManager->getPlanByName($planName);

        if ($plan->isFree()) {
            return new JsonResponse(['free' => true]);
        }

        $subscriber = $subscriberProvider->getSubscriber();
        $subscription = $subscriptionFactory->createFromPlanAndPaymentSchedule($plan, $paymentSchedule);
        $options = $subscriptionOptionsFactory->getOptions($plan, $paymentSchedule);
        $checkout = $checkoutManager->createCheckoutForSubscription($subscription, $options, $seats);

        $subscriber->setSubscription($subscription);
        $subscriberRepository->save($subscriber);

        return new JsonResponse(['id' => $checkout->getId()]);
    }

    #[Route('/payments/success/{checkoutId}', name: 'parthenon_payment_checkout_success', requirements: ['checkoutId' => '\w+'])]
    public function success(
        Request $request,
        LoggerInterface $logger,
        CurrentSubscriberProviderInterface $subscriberProvider,
        SubscriberRepositoryInterface $subscriberRepository,
        CheckoutManagerInterface $checkoutManager,
        UrlGeneratorInterface $urlGenerator,
        EventDispatcherInterface $dispatcher,
        ParameterBagInterface $parameterBag,
        ?string $checkoutId = null,
    ) {
        $subscriber = $subscriberProvider->getSubscriber();
        if ($checkoutId) {
            if (!$subscriber->getSubscription()->getCheckoutId()) {
                $logger->warning("The subscriber doesn't have a checkout id");

                return new RedirectResponse('/');
            }

            if ($subscriber->getSubscription()->getCheckoutId() !== $checkoutId) {
                $logger->warning("The checkout ids don't match");

                return new RedirectResponse('/');
            }
        }

        $checkoutManager->handleSuccess($subscriber->getSubscription());

        $logger->info('A subscriber has successfully paid');

        $subscriberRepository->save($subscriber);
        $dispatcher->dispatch(new PaymentSuccessEvent($subscriber), PaymentSuccessEvent::NAME);

        return new RedirectResponse($urlGenerator->generate($parameterBag->get('parthenon_payments_success_redirect_route')));
    }

    #[Route('/payments/plans/change/{planName}/{paymentSchedule}', name: 'parthenon_payment_change')]
    public function changeSubscription(
        Request $request,
        LoggerInterface $logger,
        CurrentSubscriberProviderInterface $subscriberProvider,
        SubscriberRepositoryInterface $subscriberRepository,
        SubscriptionManagerInterface $subscriptionManager,
        PlanManager $planManager,
        PriceProviderInterface $priceProvider,
    ) {
        try {
            $planName = $request->get('planName');
            $paymentSchedule = $request->get('paymentSchedule');

            $logger->info('Changing a subscription to a different plan/payment schedule', ['planName' => $planName, 'paymentSchedule' => $paymentSchedule]);

            $plan = $planManager->getPlanByName($planName);

            $subscriber = $subscriberProvider->getSubscriber();

            $subscriber->getSubscription()->setPriceId($priceProvider->getPriceId($plan, $paymentSchedule));
            $subscriber->getSubscription()->setPlanName($planName);
            $subscriber->getSubscription()->setPaymentSchedule($paymentSchedule);
            if ($plan->isFree()) {
                $subscriptionManager->cancel($subscriber->getSubscription());
            } else {
                $subscriptionManager->change($subscriber->getSubscription());
            }
            $subscriberRepository->save($subscriber);
        } catch (\Throwable $e) {
            $logger->error('Unable to change payment', ['error_message' => $e->getMessage()]);

            return new JsonResponse(['success' => false]);
        }

        return new JsonResponse(['success' => true, 'plan' => ['plan_name' => $planName, 'payment_schedule' => $paymentSchedule, 'status' => $subscriber->getSubscription()->getStatus()]]);
    }

    #[Route('/payments/portal', name: 'parthenon_payments_billing_portal')]
    public function redirectToBillingPortal(
        Request $request,
        CurrentSubscriberProviderInterface $currentSubscriberProvider,
        SubscriptionManagerInterface $subscriptionManager,
    ) {
        $subscriber = $currentSubscriberProvider->getSubscriber();
        $url = $subscriptionManager->getBillingPortal($subscriber->getSubscription());

        return new RedirectResponse($url);
    }

    #[Route('/payments/checkout/cancel', name: 'parthenon_payments_cancel_checkout')]
    public function cancelCheckout(
        LoggerInterface $logger,
        UrlGeneratorInterface $urlGenerator,
        ParameterBagInterface $parameterBag,
    ) {
        $logger->info('A user has returned from the checkout by cancelling');

        return new RedirectResponse($urlGenerator->generate($parameterBag->get('parthenon_payments_cancel_checkout_redirect_route')));
    }

    #[Route('/payments/cancel', name: 'parthenon_payments_cancel')]
    public function cancel(
        Request $request,
        LoggerInterface $logger,
        CurrentSubscriberProviderInterface $subscriberProvider,
        SubscriberRepositoryInterface $subscriberRepository,
        SubscriptionManagerInterface $subscriptionManager,
    ) {
        $logger->info('A user has cancelled their subscription');

        $subscriber = $subscriberProvider->getSubscriber();
        $subscriptionManager->cancel($subscriber->getSubscription());
        $subscriberRepository->save($subscriber);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/payments/plans', name: 'parthenon_payments_plans')]
    public function listAction(PlanManager $planManager, ConfigInterface $config, CurrentSubscriberProviderInterface $currentSubscriberProvider, PriceProviderInterface $priceProvider)
    {
        $plans = $planManager->getPlans();

        $output = [];

        foreach ($plans as $plan) {
            $output[$plan->getName()] = [
                'name' => $plan->getName(),
                'limits' => $plan->getLimits(),
                'prices' => $priceProvider->getPrices($plan),
            ];
        }

        $subscriber = $currentSubscriberProvider->getSubscriber();

        return new JsonResponse([
            'plans' => $output,
            'current_plan' => [
                'plan_name' => $subscriber->getSubscription()->getPlanName(),
                'status' => $subscriber->getSubscription()->getStatus(),
                'payment_schedule' => $subscriber->getSubscription()->getPaymentSchedule(),
            ],
            'provider' => $config->getConfigPublicPayload(),
        ]);
    }
}
