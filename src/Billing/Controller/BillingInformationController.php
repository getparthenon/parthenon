<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

use Parthenon\Billing\Customer\CustomerRegisterInterface;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\Common\Address;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BillingInformationController
{
    #[Route('/billing/details', name: 'parthenon_billing_billinginformation_getbillingdetails', methods: ['GET'])]
    public function getBillingDetails(
        Request $request,
        SerializerInterface $serializer,
        CustomerProviderInterface $customerProvider,
    ) {
        $customer = $customerProvider->getCurrentCustomer();

        $data = $serializer->serialize(['address' => $customer->getBillingAddress()], 'json');

        return JsonResponse::fromJsonString($data);
    }

    #[Route('/billing/details', name: 'parthenon_billing_billinginformation_setbillingdetails', methods: ['POST'])]
    public function setBillingDetails(
        Request $request,
        SerializerInterface $serializer,
        CustomerRepositoryInterface $customerRepository,
        CustomerProviderInterface $customerProvider,
        CustomerRegisterInterface $customerRegister,
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
        $customerRegister->updateCustomer($customer);
        $customerRepository->save($customer);

        return new JsonResponse(['success' => true], JsonResponse::HTTP_ACCEPTED);
    }
}
