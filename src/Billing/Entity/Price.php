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

namespace Parthenon\Billing\Entity;

use Brick\Money\Currency;
use Brick\Money\Money;
use Parthenon\Athena\Entity\CrudEntityInterface;
use Parthenon\Athena\Entity\DeletableInterface;

class Price implements CrudEntityInterface, DeletableInterface
{
    protected ?string $paymentProviderDetailsUrl;
    private $id;

    private int $amount;

    private string $currency;

    private bool $recurring;

    private ?string $schedule = null;

    private ?string $externalReference = null;

    private bool $includingTax = true;

    private Product $product;

    private ?bool $public = true;

    private ?bool $isDeleted = false;

    private \DateTimeInterface $createdAt;

    private ?\DateTimeInterface $deletedAt = null;

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

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return strtoupper($this->currency);
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function hasExternalReference(): bool
    {
        return isset($this->externalReference);
    }

    public function isRecurring(): bool
    {
        return $this->recurring;
    }

    public function setRecurring(bool $recurring): void
    {
        $this->recurring = $recurring;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function isIncludingTax(): bool
    {
        return $this->includingTax;
    }

    public function setIncludingTax(bool $includingTax): void
    {
        $this->includingTax = $includingTax;
    }

    public function getAsMoney(): Money
    {
        return Money::ofMinor($this->amount, Currency::of($this->currency));
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function isPublic(): bool
    {
        return true === $this->public;
    }

    public function setPublic(?bool $public): void
    {
        $this->public = $public;
    }

    public function getPaymentProviderDetailsUrl(): ?string
    {
        return $this->paymentProviderDetailsUrl;
    }

    public function setPaymentProviderDetailsUrl(?string $paymentProviderDetailsUrl): void
    {
        $this->paymentProviderDetailsUrl = $paymentProviderDetailsUrl;
    }

    public function getDisplayName(): string
    {
        if ($this->recurring) {
            $type = 'EmbeddedSubscription - '.$this->schedule;
        } else {
            $type = 'one-off';
        }

        return (string) $this->getAsMoney().' - '.$type.' - '.$this->getProduct()?->getName();
    }

    public function setDeletedAt(\DateTimeInterface $dateTime): DeletableInterface
    {
        $this->deletedAt = $dateTime;
    }

    public function isDeleted(): bool
    {
        return true === $this->isDeleted;
    }

    public function markAsDeleted(): DeletableInterface
    {
        $this->deletedAt = new \DateTime('now');
        $this->isDeleted = true;

        return $this;
    }

    public function unmarkAsDeleted(): DeletableInterface
    {
        $this->deletedAt = null;
        $this->isDeleted = false;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
