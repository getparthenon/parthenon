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

namespace Parthenon\Billing\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Parthenon\Athena\Entity\CrudEntityInterface;

class SubscriptionPlan implements CrudEntityInterface, SubscriptionPlanInterface
{
    private $id;

    private bool $public = false;

    private string $name;

    private ?string $codeName = null;

    private ?string $externalReference = null;

    private ?string $paymentProviderDetailsLink = null;

    private array|Collection $limits;

    private bool $perSeat;

    private bool $free;

    private int $userCount;

    private ?bool $hasTrial = false;

    private ?int $trialLengthDays = 0;

    private array|Collection $features;

    private array|Collection $prices;

    private ProductInterface $product;

    public function __construct()
    {
        $this->limits = new ArrayCollection();
        $this->features = new ArrayCollection();
        $this->prices = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCodeName(): ?string
    {
        return $this->codeName;
    }

    public function setCodeName(?string $codeName): void
    {
        $this->codeName = $codeName;
    }

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function hasExternalReference(): bool
    {
        return isset($this->externalReference);
    }

    public function getPaymentProviderDetailsLink(): ?string
    {
        return $this->paymentProviderDetailsLink;
    }

    public function setPaymentProviderDetailsLink(?string $paymentProviderDetailsLink): void
    {
        $this->paymentProviderDetailsLink = $paymentProviderDetailsLink;
    }

    public function getLimits(): Collection|array
    {
        return $this->limits;
    }

    /**
     * @param SubscriptionPlanLimit[]|Collection $limits
     */
    public function setLimits(Collection|array $limits): void
    {
        $this->limits = $limits;
    }

    public function addLimit(SubscriptionPlanLimit $limit): void
    {
        if (!$this->limits->contains($limit)) {
            $limit->setSubscriptionPlan($this);
            $this->limits->add($limit);
        }
    }

    public function removeLimit(SubscriptionPlanLimit $limit): void
    {
        $this->limits->removeElement($limit);
    }

    public function addFeature(SubscriptionFeature $subscriptionFeature): void
    {
        if (!$this->features->contains($subscriptionFeature)) {
            $this->features->add($subscriptionFeature);
        }
    }

    public function removeFeature(SubscriptionFeature $subscriptionFeature): void
    {
        $this->features->removeElement($subscriptionFeature);
    }

    public function addPrice(Price $price): void
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
        }
    }

    public function getPriceForCurrencyAndSchedule(string $currency, string $schedule): Price
    {
        foreach ($this->getPrices() as $price) {
            if (strtolower($price->getCurrency()) === strtolower($currency) && strtolower($price->getSchedule()) === strtolower($schedule)) {
                return $price;
            }
        }
        throw new \Exception("Can't found price");
    }

    public function removePrice(Price $price): void
    {
        $this->prices->removeElement($price);
    }

    public function getPrices(): Collection|array
    {
        return $this->prices;
    }

    public function setPrices(Collection|array $prices): void
    {
        $this->prices = $prices;
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }

    public function isPerSeat(): bool
    {
        return $this->perSeat;
    }

    public function setPerSeat(bool $perSeat): void
    {
        $this->perSeat = $perSeat;
    }

    public function isFree(): bool
    {
        return $this->free;
    }

    public function setFree(bool $free): void
    {
        $this->free = $free;
    }

    public function getUserCount(): int
    {
        return $this->userCount;
    }

    public function setUserCount(int $userCount): void
    {
        $this->userCount = $userCount;
    }

    public function getFeatures(): Collection|array
    {
        return $this->features;
    }

    public function setFeatures(Collection|array $features): void
    {
        $this->features = $features;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    public function getHasTrial(): bool
    {
        return true === $this->hasTrial;
    }

    public function setHasTrial(?bool $hasTrial): void
    {
        $this->hasTrial = $hasTrial;
    }

    public function getTrialLengthDays(): int
    {
        return (int) $this->trialLengthDays;
    }

    public function setTrialLengthDays(?int $trialLengthDays): void
    {
        $this->trialLengthDays = $trialLengthDays;
    }
}
