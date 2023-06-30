<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Controller;

use Parthenon\Billing\Config\FrontendConfig;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\PaymentMethod\DefaultPaymentManagerInterface;
use Parthenon\Billing\PaymentMethod\DeleterInterface;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PaymentDetailsController
{
    #[Route('/billing/payment-method', name: 'parthenon_billing_paymentdetails_fetch_payment_details', methods: ['GET'])]
    public function fetchPaymentDetails(
        Request $request,
        CustomerProviderInterface $customerProvider,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        SerializerInterface $serializer,
    ) {
        $customer = $customerProvider->getCurrentCustomer();
        $paymentDetails = $paymentDetailsRepository->getPaymentCardForCustomer($customer);
        $returnData = $serializer->serialize(['payment_details' => $paymentDetails], 'json');

        return JsonResponse::fromJsonString($returnData);
    }

    #[Route('/billing/payment-method/token/start', name: 'parthenon_billing_paymentdetails_starttokenprocess', methods: ['GET'])]
    public function startTokenProcess(
        Request $request,
        LoggerInterface $logger,
        CustomerProviderInterface $customerProvider,
        FrontendConfig $config,
        FrontendAddProcessorInterface $addCardByTokenDriver,
    ) {
        $logger->info('Starting the card token process');

        $customer = $customerProvider->getCurrentCustomer();
        $token = $addCardByTokenDriver->startTokenProcess($customer);

        return new JsonResponse([
            'token' => $token,
            'api_info' => $config->getApiInfo(),
        ]);
    }

    #[Route('/billing/payment-method/token/add', name: 'parthenon_billing_paymentdetails_addcardbytoken', methods: ['POST'])]
    public function addCardByToken(
        Request $request,
        CustomerProviderInterface $customerProvider,
        SerializerInterface $serializer,
        FrontendAddProcessorInterface $addCardByTokenDriver
    ) {
        $customer = $customerProvider->getCurrentCustomer();

        $data = json_decode($request->getContent(), true);

        $paymentDetails = $addCardByTokenDriver->createPaymentDetailsFromToken($customer, $data['token']);
        $json = $serializer->serialize(['success' => true, 'payment_details' => $paymentDetails], 'json');

        return JsonResponse::fromJsonString($json, JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/billing/payment-method/{id}', name: 'parthenon_billing_paymentdetails_deletecard', methods: ['DELETE'])]
    public function deleteCard(Request $request, PaymentCardRepositoryInterface $paymentDetailsRepository, DeleterInterface $deleter)
    {
        try {
            /** @var PaymentCard $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }
        $deleter->delete($paymentDetails);

        return new JsonResponse(['success' => true], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/billing/payment-method/{id}/default', name: 'parthenon_billing_paymentdetails_defaultcard', methods: ['POST'])]
    public function defaultCard(Request $request, CustomerProviderInterface $customerProvider, PaymentCardRepositoryInterface $paymentDetailsRepository, DefaultPaymentManagerInterface $defaultPaymentManager)
    {
        $customer = $customerProvider->getCurrentCustomer();
        try {
            /** @var PaymentCard $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $defaultPaymentManager->makePaymentDetailsDefault($customer, $paymentDetails);

        return new JsonResponse(['success' => true], JsonResponse::HTTP_ACCEPTED);
    }
}
