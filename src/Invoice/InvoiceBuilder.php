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

namespace Parthenon\Invoice;

use Brick\Money\Money;
use Parthenon\Common\Address;

final class InvoiceBuilder
{
    /**
     * @var ItemInterface[]
     */
    private array $items = [];

    private string $vatNumber;

    private CountryRules $countryRules;

    private Address $billerAddress;

    private float $vatRate = 0.0;

    private bool $addVat = true;

    private \DateTimeInterface $createdAt;

    private string $currency = 'EUR';

    private InvoiceNumberGeneratorInterface $invoiceNumberGenerator;

    private $invoiceNumber;

    private string $deliveryAddress;

    private string $paymentDetails;

    public function setInvoiceNumberGenerator(InvoiceNumberGeneratorInterface $invoiceNumberGenerator): void
    {
        $this->invoiceNumberGenerator = $invoiceNumberGenerator;
    }

    public function setInvoiceNumber($invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function addVatToTotal(): self
    {
        $this->addVat = true;

        return $this;
    }

    public function deductVatFromTotal(): self
    {
        $this->addVat = false;

        return $this;
    }

    public function addItem(string $name, Money $value, array $options = []): self
    {
        $quantity = $options['quantity'] ?? 1;
        $vat = $options['vat'] ?? $this->vatRate;
        $type = $options['type'] ?? '';
        $description = $options['description'] ?? '';

        $item = new Item($name, $description, $value, $quantity, $vat, $this->currency);
        $item->setType($type);

        $this->items[] = $item;

        return $this;
    }

    public function build(): Invoice
    {
        $invoice = new Invoice($this->addVat);
        $invoice->setCurrency($this->currency);

        if (isset($this->deliveryAddress)) {
            $invoice->setDeliveryAddress($this->deliveryAddress);
        }

        if (isset($this->billerAddress)) {
            $invoice->setBillerAddress($this->billerAddress);
        }

        if (isset($this->createdAt)) {
            $invoice->setCreatedAt($this->createdAt);
        }

        if (isset($this->vatNumber)) {
            $invoice->setVatNumber($this->vatNumber);
        }

        if (isset($this->paymentDetails)) {
            $invoice->setPaymentDetails($this->paymentDetails);
        }

        foreach ($this->items as $item) {
            if (isset($this->billerAddress) && isset($this->countryRules)) {
                $this->countryRules->handleRules($item, $this->billerAddress);
            }
            $invoice->addItem($item);
        }

        if (isset($this->invoiceNumber)) {
            $invoice->setInvoiceNumber($this->invoiceNumber);
        } elseif (isset($this->invoiceNumberGenerator)) {
            $invoice->setInvoiceNumber($this->invoiceNumberGenerator->generateNumber($invoice));
        }

        return $invoice;
    }

    public function addVatNumber(string $vatNumber)
    {
        $this->vatNumber = $vatNumber;
    }

    public function setBillerAddress(Address $address)
    {
        $this->billerAddress = $address;
    }

    public function setCountryRules(CountryRules $countryRules): void
    {
        $this->countryRules = $countryRules;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function setDeliveryAddress(string $deliveryAddress): void
    {
        $this->deliveryAddress = $deliveryAddress;
    }

    public function setPaymentDetails(string $paymentDetails): void
    {
        $this->paymentDetails = $paymentDetails;
    }
}
