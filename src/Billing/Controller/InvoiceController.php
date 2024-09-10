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

use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Invoice\InvoiceChargerInterface;
use Parthenon\Billing\Invoice\InvoiceProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class InvoiceController
{
    #[Route('/billing/invoices', name: 'parthenon_billing_invoices_list')]
    public function listAction(
        CustomerProviderInterface $customerProvider,
        InvoiceProviderInterface $invoiceProvider,
        SerializerInterface $serializer,
        #[Autowire('%parthenon_billing_billabear_enabled%')]
        bool $supported,
    ): Response {
        $customer = $customerProvider->getCurrentCustomer();
        $invoices = $invoiceProvider->fetchInvoices($customer);

        $returnData = $serializer->serialize(['invoices' => $invoices, 'supported' => $supported], 'json');

        return JsonResponse::fromJsonString($returnData);
    }

    #[Route('/billing/invoices/{id}/download', name: 'parthenon_billing_invoices_download')]
    public function downloadAction(
        Request $request,
        InvoiceProviderInterface $invoiceProvider,
    ): Response {
        $download = $invoiceProvider->downloadInvoiceById($request->attributes->get('id'));

        $tmpFile = tempnam('/tmp', 'pdf');
        file_put_contents($tmpFile, $download);

        $response = new BinaryFileResponse($tmpFile);
        $filename = sprintf('invoice-%s.pdf', $request->get('id'));

        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }

    #[Route('/billing/invoices/{id}/charge', name: 'parthenon_billing_invoices_charge')]
    public function chargeAction(
        Request $request,
        InvoiceChargerInterface $invoiceCharger,
    ): Response {
        $paid = $invoiceCharger->chargeInvoice($request->get('id'));

        return new JsonResponse(['paid' => $paid]);
    }
}
