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
