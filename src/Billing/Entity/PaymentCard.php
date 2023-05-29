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

use Parthenon\Athena\Entity\DeletableInterface;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

class PaymentCard implements DeletableInterface
{
    protected $id;

    #[Ignore]
    protected $customer;

    #[Ignore]
    protected string $provider;

    #[Ignore]
    protected string $storedCustomerReference;

    #[Ignore]
    protected string $storedPaymentReference;

    protected ?string $name = null;

    #[SerializedName('default')]
    protected bool $defaultPaymentOption = true;

    protected ?string $brand = null;

    #[SerializedName('last_four')]
    protected ?string $lastFour = null;

    #[SerializedName('expiry_month')]
    protected ?string $expiryMonth = null;

    #[SerializedName('expiry_year')]
    protected ?string $expiryYear = null;

    #[SerializedName('created_at')]
    protected \DateTimeInterface $createdAt;

    #[Ignore]
    protected bool $deleted = false;

    #[Ignore]
    protected ?\DateTime $deletedAt = null;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function getStoredPaymentReference(): string
    {
        return $this->storedPaymentReference;
    }

    public function setStoredPaymentReference(string $storedPaymentReference): void
    {
        $this->storedPaymentReference = $storedPaymentReference;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function isDefaultPaymentOption(): bool
    {
        return $this->defaultPaymentOption;
    }

    public function setDefaultPaymentOption(bool $defaultPaymentOption): void
    {
        $this->defaultPaymentOption = $defaultPaymentOption;
    }

    public function getStoredCustomerReference(): string
    {
        return $this->storedCustomerReference;
    }

    public function setStoredCustomerReference(string $storedCustomerReference): void
    {
        $this->storedCustomerReference = $storedCustomerReference;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getLastFour(): ?string
    {
        return $this->lastFour;
    }

    public function setLastFour(?string $lastFour): void
    {
        $this->lastFour = $lastFour;
    }

    public function getExpiryMonth(): ?string
    {
        return $this->expiryMonth;
    }

    public function setExpiryMonth(?string $expiryMonth): void
    {
        $this->expiryMonth = $expiryMonth;
    }

    public function getExpiryYear(): ?string
    {
        return $this->expiryYear;
    }

    public function setExpiryYear(?string $expiryYear): void
    {
        $this->expiryYear = $expiryYear;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function setDeletedAt(\DateTimeInterface $dateTime): DeletableInterface
    {
        $this->deletedAt = $dateTime;
    }

    public function markAsDeleted(): DeletableInterface
    {
        $this->deletedAt = new \DateTime();
        $this->deleted = true;
    }

    public function unmarkAsDeleted(): DeletableInterface
    {
        $this->deletedAt = null;
        $this->deleted = true;
    }
}
