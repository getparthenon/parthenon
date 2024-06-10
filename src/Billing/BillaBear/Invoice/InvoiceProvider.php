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

namespace Parthenon\Billing\BillaBear\Invoice;

use BillaBear\Model\InlineResponse2004Data;
use Parthenon\Billing\BillaBear\SdkFactory;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Invoice\Invoice;
use Parthenon\Billing\Invoice\InvoiceProviderInterface;

class InvoiceProvider implements InvoiceProviderInterface
{
    public function __construct(private SdkFactory $sdkFactory)
    {
    }

    public function fetchInvoices(CustomerInterface $customer, mixed $lastKey = null): array
    {
        $response = $this->sdkFactory->createCustomersApi()->listCustomerInvoices($customer->getExternalCustomerReference());

        $output = [];
        foreach ($response->getData() as $invoice) {
            $output[] = $this->buildDto($invoice);
        }

        return $output;
    }

    public function downloadInvoiceById(string $id): mixed
    {
        $downloadBlob = $this->sdkFactory->createInvoiceApi()->downloadInvoice($id);

        return $downloadBlob;
    }

    protected function buildDto(InlineResponse2004Data $invoiceData): Invoice
    {
        $invoice = new Invoice();
        $invoice->setId($invoiceData->getId());
        $invoice->setCurrency($invoiceData->getCurrency());
        $invoice->setAmount($invoiceData->getAmountDue());
        $invoice->setPaid($invoiceData->getPaid());
        $invoice->setCreatedAt(new \DateTime($invoiceData->getCreatedAt()));

        return $invoice;
    }
}
