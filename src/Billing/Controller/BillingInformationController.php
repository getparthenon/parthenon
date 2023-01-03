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
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\Common\Address;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BillingInformationController
{
    #[Route('/billing/address', name: 'parthenon_billing_billinginformation_getbillingaddress', methods: ['GET'])]
    public function getBillingAddress(
        Request $request,
        SerializerInterface $serializer,
        CustomerProviderInterface $customerProvider,
    ) {
        $customer = $customerProvider->getCurrentCustomer();

        $data = $serializer->serialize(['address' => $customer->getBillingAddress()], 'json');

        return new JsonResponse($data, json: true);
    }

    #[Route('/billing/address', name: 'parthenon_billing_billinginformation_setbillingaddress', methods: ['POST'])]
    public function setBillingAddress(
        Request $request,
        SerializerInterface $serializer,
        CustomerRepositoryInterface $customerRepository,
        CustomerProviderInterface $customerProvider,
        ValidatorInterface $validator,
    ) {
        $customer = $customerProvider->getCurrentCustomer();
        $address = $serializer->deserialize($request->getContent(), Address::class, 'json');

        $violationList = $validator->validate($address);

        if (0 !== count($violationList)) {
            $errors = [];
            /** @var ConstraintViolation $item */
            foreach ($violationList as $item) {
                $key = $item->getPropertyPath();

                if (!isset($errors[$key])) {
                    $errors[$key] = [];
                }

                $errors[$key][] = (string) $item->getMessage();
            }

            return new JsonResponse(['success' => false, 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $customer->setBillingAddress($address);

        $customerRepository->save($customer);

        return new JsonResponse(['success' => true]);
    }
}
