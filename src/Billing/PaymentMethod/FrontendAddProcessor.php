<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\PaymentMethod;

use Obol\Model\CardDetails;
use Obol\Model\Customer as ObolCustomer;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Event\PaymentCardAdded;
use Parthenon\Billing\Factory\PaymentMethodFactoryInterface;
use Parthenon\Billing\Obol\CustomerConverterInterface;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FrontendAddProcessor implements FrontendAddProcessorInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private CustomerRepositoryInterface $customerRepository,
        private CustomerConverterInterface $customerConverter,
        private PaymentMethodFactoryInterface $paymentMethodFactory,
        private PaymentCardRepositoryInterface $paymentDetailsRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function startTokenProcess(CustomerInterface $customer): string
    {
        $billingDetails = $this->customerConverter->convertToBillingDetails($customer);
        if (!$customer->hasExternalCustomerReference()) {
            $obolCustomer = new ObolCustomer();
            $obolCustomer->setEmail($customer->getBillingEmail());
            $obolCustomer->setAddress($billingDetails->getAddress());
            $customerCreation = $this->provider->customers()->create($obolCustomer);

            $customer->setExternalCustomerReference($customerCreation->getReference());
            $customer->setPaymentProviderDetailsUrl($customerCreation->getDetailsUrl());
        }

        $tokenData = $this->provider->payments()->startFrontendCreateCardOnFile($billingDetails);
        if ($tokenData->hasCustomerCreation()) {
            $customer->setPaymentProviderDetailsUrl($tokenData->getCustomerCreation()->getDetailsUrl());
            $customer->setExternalCustomerReference($tokenData->getCustomerReference());
        }
        $this->customerRepository->save($customer);

        return $tokenData->getToken();
    }

    public function createPaymentDetailsFromToken(CustomerInterface $customer, string $token): PaymentCard
    {
        $billingDetails = $this->customerConverter->convertToBillingDetails($customer);
        $billingDetails->setCardDetails(new CardDetails());
        $billingDetails->getCardDetails()->setToken($token);

        $response = $this->provider->payments()->createCardOnFile($billingDetails);
        $cardFile = $response->getCardFile();
        $paymentCard = $this->paymentMethodFactory->buildFromCardFile($customer, $cardFile, $this->provider->getName());

        if ($response->hasCustomerCreation()) {
            $customer->setPaymentProviderDetailsUrl($response->getCustomerCreation()->getDetailsUrl());
            $customer->setExternalCustomerReference($response->getCustomerCreation()->getReference());
        }

        if ($paymentCard->isDefaultPaymentOption()) {
            $this->paymentDetailsRepository->markAllCustomerCardsAsNotDefault($customer);
        }
        $this->paymentDetailsRepository->save($paymentCard);

        $this->eventDispatcher->dispatch(new PaymentCardAdded($customer, $paymentCard), PaymentCardAdded::NAME);

        return $paymentCard;
    }
}
