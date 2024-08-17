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

namespace Parthenon\Billing\Entity;

use Brick\Money\Currency;
use Brick\Money\Money;
use Doctrine\Common\Collections\Collection;
use Parthenon\Athena\Entity\CrudEntityInterface;
use Parthenon\Athena\Entity\DeletableInterface;
use Parthenon\Billing\Enum\PriceType;

class Price implements CrudEntityInterface, DeletableInterface, PriceInterface
{
    protected $id;

    protected ?int $amount;

    protected string $currency;

    protected bool $recurring;

    protected ?string $schedule = null;

    protected ?string $externalReference = null;

    protected bool $includingTax = true;

    protected Product $product;

    protected ?bool $public = true;

    protected ?bool $isDeleted = false;

    protected \DateTimeInterface $createdAt;

    protected ?\DateTimeInterface $deletedAt = null;

    protected ?string $paymentProviderDetailsUrl = null;

    protected array|Collection $tierComponents = [];

    protected PriceType $type;

    private ?int $units = null;

    private ?bool $usage = false;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
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

    public function getTierComponents(): Collection|array
    {
        return $this->tierComponents;
    }

    public function setTierComponents(Collection|array $tierComponents): void
    {
        $this->tierComponents = $tierComponents;
    }

    public function getType(): PriceType
    {
        return $this->type;
    }

    public function setType(PriceType $type): void
    {
        $this->type = $type;
    }

    public function getUnits(): ?int
    {
        return $this->units;
    }

    public function setUnits(?int $units): void
    {
        $this->units = $units;
    }

    public function getUsage(): bool
    {
        return true === $this->usage;
    }

    public function setUsage(?bool $usage): void
    {
        $this->usage = $usage;
    }
}
