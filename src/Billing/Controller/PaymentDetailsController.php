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
use Parthenon\Billing\Obol\CustomerConverterInterface;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentDetailsController
{
    #[Route('/billing/card/add', name: 'parthenon_billing_paymentdetails_addcarddetails', methods: ['POST'])]
    public function addCardDetails(Request $request, ProviderInterface $provider)
    {
    }

    #[Route('/billing/card/token/start', name: 'parthenon_billing_paymentdetails_starttokenprocess', methods: ['GET'])]
    public function startTokenProcess(
        Request $request,
        LoggerInterface $logger,
        ProviderInterface $provider,
        CustomerProviderInterface $customerProvider,
        CustomerRepositoryInterface $customerRepository,
        CustomerConverterInterface $customerConverter,
        FrontendConfig $config,
    ) {
        $logger->info('Starting the card token process');

        $customer = $customerProvider->getCurrentCustomer();
        $billingDetails = $customerConverter->convertToBillingDetails($customer);

        $tokenData = $provider->payments()->startFrontendCreateCardOnFile($billingDetails);
        $customer->setExternalCustomerReference($tokenData->getCustomerReference());

        $customerRepository->save($customer);

        return new JsonResponse([
            'token' => $tokenData->getToken(),
            'api_info' => $config->getApiInfo(),
        ]);
    }

    #[Route('/billing/card/token/add', name: 'parthenon_billing_paymentdetails_addcardbytoken', methods: ['POST'])]
    public function addCardByToken(
        Request $request,
        ProviderInterface $provider,
        CustomerProviderInterface $customerProvider,
        CustomerConverterInterface $customerConverter,
        PaymentDetailsRepositoryInterface $detailsRepository,
    ) {
        $data = json_decode($request->getContent(), true);
        $customer = $customerProvider->getCurrentCustomer();
        $billingDetails = $customerConverter->convertToBillingDetails($customer);
        $billingDetails->setCardDetails(new CardDetails());
        $billingDetails->getCardDetails()->setToken($data['token']);

        $response = $provider->payments()->createCardOnFile($billingDetails);
        $cardFile = $response->getCardFile();
        $storedPaymentReference = $cardFile->getStoredPaymentReference();

        $paymentDetails = new PaymentDetails();
        $paymentDetails->setCustomer($customer);
        $paymentDetails->setStoredCustomerReference($customer->getExternalCustomerReference());
        $paymentDetails->setStoredPaymentReference($storedPaymentReference);
        $paymentDetails->setProvider($provider->getName());
        $paymentDetails->setDefaultPaymentOption(true);
        $paymentDetails->setName('Default');
        $paymentDetails->setBrand($cardFile->getBrand());
        $paymentDetails->setLastFour($cardFile->getLastFour());
        $paymentDetails->setExpiryMonth($cardFile->getExpiryMonth());
        $paymentDetails->setExpiryYear($cardFile->getExpiryYear());
        $paymentDetails->setDeleted(false);
        $paymentDetails->setCreatedAt(new \DateTime());

        $detailsRepository->save($paymentDetails);

        return new JsonResponse(['success' => true]);
    }
}
