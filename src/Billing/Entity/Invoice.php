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

namespace Parthenon\Billing\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Parthenon\Common\Address;

class Invoice
{
    private $id;

    private string $invoiceNumber;

    private bool $valid;

    private Address $billerAddress;

    private Address $payeeAddress;

    private CustomerInterface $customer;

    private array|Collection $payments;

    private array|Collection $lines;

    private ?string $comment;

    private string $currency;

    private int $total;

    private int $subTotal;

    private int $vatTotal;

    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->payments = new ArrayCollection([]);
        $this->lines = new ArrayCollection([]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getBillerAddress(): Address
    {
        return $this->billerAddress;
    }

    public function setBillerAddress(Address $billerAddress): void
    {
        $this->billerAddress = $billerAddress;
    }

    public function getPayeeAddress(): Address
    {
        return $this->payeeAddress;
    }

    public function setPayeeAddress(Address $payeeAddress): void
    {
        $this->payeeAddress = $payeeAddress;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function addPayment(Payment $payment): void
    {
        $this->payments->add($payment);
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection|array
    {
        return $this->payments;
    }

    public function setPayments(Collection|array $payments): void
    {
        $this->payments = $payments;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function getSubTotal(): int
    {
        return $this->subTotal;
    }

    public function setSubTotal(int $subTotal): void
    {
        $this->subTotal = $subTotal;
    }

    public function getVatTotal(): int
    {
        return $this->vatTotal;
    }

    public function setVatTotal(int $vatTotal): void
    {
        $this->vatTotal = $vatTotal;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Collection|InvoiceLine[]
     */
    public function getLines(): Collection|array
    {
        return $this->lines;
    }

    public function setLines(Collection|array $lines): void
    {
        $this->lines = $lines;
    }
}
