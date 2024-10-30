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

use Brick\Math\RoundingMode;
use Brick\Money\Context\AutoContext;
use Brick\Money\Context\DefaultContext;
use Brick\Money\Money;
use Parthenon\Common\Address;

final class Invoice
{
    /**
     * @var ItemInterface[]
     */
    private array $items = [];
    private string $vatNumber;
    private ?Address $billerAddress = null;
    private Address $billingAddress;
    private Address $shippingAddress;
    private bool $addVat;
    private ?\DateTimeInterface $createdAt = null;
    private $invoiceNumber;
    private string $currency = 'EUR';
    private string $deliveryAddress = '';
    private string $paymentDetails = '';

    public function __construct(bool $addVat = true)
    {
        $this->addVat = $addVat;
    }

    public function addItem(ItemInterface $item)
    {
        $this->items[] = $item;
    }

    public function getTotal(): string
    {
        $total = $this->calculateTotal();
        if (!isset($this->vatNumber)) {
            if ($this->addVat) {
                $total = $total->plus($this->calculateVat());
            } else {
                $total = $total->minus($this->calculateVat());
            }
        }

        return $total->getAmount()->__toString();
    }

    public function getSubTotal(): string
    {
        return $this->calculateTotal()->getAmount()->__toString();
    }

    public function getVat(): string
    {
        if (isset($this->vatNumber)) {
            return Money::zero($this->currency)->getAmount()->__toString();
        }

        $vatAmount = $this->calculateVat();

        return $vatAmount->getAmount()->__toString();
    }

    public function setVatNumber(string $vatNumber)
    {
        $this->vatNumber = $vatNumber;
    }

    /**
     * @return ItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function setBillerAddress(Address $billerAddress): void
    {
        $this->billerAddress = $billerAddress;
    }

    public function getBillerAddress(): ?Address
    {
        return $this->billerAddress;
    }

    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(Address $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }

    public function getShippingAddress(): Address
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(Address $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getDeliveryAddress(): string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): void
    {
        $this->deliveryAddress = $deliveryAddress;
    }

    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber($invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function getPaymentDetails(): string
    {
        return $this->paymentDetails;
    }

    public function setPaymentDetails(string $paymentDetails): void
    {
        $this->paymentDetails = $paymentDetails;
    }

    private function calculateTotal(): Money
    {
        $total = Money::zero($this->currency);

        foreach ($this->items as $item) {
            $total = $total->plus($item->getSubTotal());
        }

        return $total;
    }

    private function calculateVat(): Money
    {
        $vat = Money::zero($this->currency, new AutoContext());

        foreach ($this->items as $item) {
            $vat = $vat->plus($item->getVat());
        }

        $vat = $vat->to(new DefaultContext(), RoundingMode::HALF_UP);

        return $vat;
    }
}
