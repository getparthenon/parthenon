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

use Obol\Model\CardDetails;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Config\FrontendConfig;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Entity\PaymentDetails;
use Parthenon\Billing\Factory\PaymentDetailsFactoryInterface;
use Parthenon\Billing\Obol\CustomerConverterInterface;
use Parthenon\Billing\PaymentDetails\AddCardByTokenDriverInterface;
use Parthenon\Billing\PaymentDetails\DefaultPaymentManagerInterface;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PaymentDetailsController
{
    #[Route('/billing/payment-details', name: 'parthenon_billing_paymentdetails_fetch_payment_details', methods: ['GET'])]
    public function fetchPaymentDetails(
        Request $request,
        CustomerProviderInterface $customerProvider,
        PaymentDetailsRepositoryInterface $paymentDetailsRepository,
        SerializerInterface $serializer,
    ) {
        $customer = $customerProvider->getCurrentCustomer();
        $paymentDetails = $paymentDetailsRepository->getPaymentDetailsForCustomer($customer);
        $returnData = $serializer->serialize(['payment_details' => $paymentDetails], 'json');

        return JsonResponse::fromJsonString($returnData);
    }

    #[Route('/billing/payment-details/token/start', name: 'parthenon_billing_paymentdetails_starttokenprocess', methods: ['GET'])]
    public function startTokenProcess(
        Request $request,
        LoggerInterface $logger,
        CustomerProviderInterface $customerProvider,
        FrontendConfig $config,
        AddCardByTokenDriverInterface $addCardByTokenDriver,
    ) {
        $logger->info('Starting the card token process');

        $customer = $customerProvider->getCurrentCustomer();
        $token = $addCardByTokenDriver->startTokenProcess($customer);

        return new JsonResponse([
            'token' => $token,
            'api_info' => $config->getApiInfo(),
        ]);
    }

    #[Route('/billing/payment-details/token/add', name: 'parthenon_billing_paymentdetails_addcardbytoken', methods: ['POST'])]
    public function addCardByToken(
        Request $request,
        ProviderInterface $provider,
        CustomerProviderInterface $customerProvider,
        CustomerConverterInterface $customerConverter,
        PaymentDetailsRepositoryInterface $detailsRepository,
        SerializerInterface $serializer,
        PaymentDetailsFactoryInterface $paymentDetailsFactory,
    ) {
        $customer = $customerProvider->getCurrentCustomer();

        $data = json_decode($request->getContent(), true);
        $billingDetails = $customerConverter->convertToBillingDetails($customer);
        $billingDetails->setCardDetails(new CardDetails());
        $billingDetails->getCardDetails()->setToken($data['token']);

        $response = $provider->payments()->createCardOnFile($billingDetails);
        $cardFile = $response->getCardFile();
        $paymentDetails = $paymentDetailsFactory->buildFromCardFile($customer, $cardFile, $provider->getName());

        if ($response->hasCustomerCreation()) {
            $customer->setPaymentProviderDetailsUrl($response->getCustomerCreation()->getDetailsUrl());
            $customer->setExternalCustomerReference($response->getCustomerReference());
        }

        if ($paymentDetails->isDefaultPaymentOption()) {
            $detailsRepository->markAllCustomerDetailsAsNotDefault($customer);
        }
        $detailsRepository->save($paymentDetails);

        $json = $serializer->serialize(['success' => true, 'payment_details' => $paymentDetails], 'json');

        return JsonResponse::fromJsonString($json, JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/billing/payment-details/{id}', name: 'parthenon_billing_paymentdetails_deletecard', methods: ['DELETE'])]
    public function deleteCard(Request $request, PaymentDetailsRepositoryInterface $paymentDetailsRepository)
    {
        try {
            /** @var PaymentDetails $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }
        $paymentDetails->setDeleted(true);
        $paymentDetailsRepository->save($paymentDetails);

        return new JsonResponse(['success' => true], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/billing/payment-details/{id}/default', name: 'parthenon_billing_paymentdetails_defaultcard', methods: ['POST'])]
    public function defaultCard(Request $request, CustomerProviderInterface $customerProvider, PaymentDetailsRepositoryInterface $paymentDetailsRepository, DefaultPaymentManagerInterface $defaultPaymentManager)
    {
        $customer = $customerProvider->getCurrentCustomer();
        try {
            /** @var PaymentDetails $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $defaultPaymentManager->makePaymentDetailsDefault($customer, $paymentDetails);

        return new JsonResponse(['success' => true], JsonResponse::HTTP_ACCEPTED);
    }
}
