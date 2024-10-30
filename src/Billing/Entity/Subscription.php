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

use Brick\Money\Currency;
use Brick\Money\Money;
use Doctrine\Common\Collections\Collection;
use Parthenon\Billing\Enum\SubscriptionStatus;

class Subscription implements SubscriptionInterface
{
    private $id;

    private CustomerInterface $customer;

    private string $planName;

    private ?string $paymentSchedule = null;

    private ?int $seats = 1;

    private bool $active;

    private SubscriptionStatus $status;

    private ?int $amount = null;

    private ?string $currency = null;

    private ?string $mainExternalReference = null;

    private ?string $mainExternalReferenceDetailsUrl = null;

    private ?string $childExternalReference = null;

    private ?PaymentCard $paymentDetails = null;

    private ?SubscriptionPlanInterface $subscriptionPlan = null;

    private ?PriceInterface $price = null;

    private \DateTime $createdAt;

    private ?\DateTime $startOfCurrentPeriod = null;

    private ?\DateTime $validUntil = null;

    private \DateTime $updatedAt;

    private ?\DateTime $endedAt = null;

    private bool $hasTrial = false;

    private ?int $trialLengthDays = 0;

    private Collection $payments;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getPlanName(): string
    {
        return $this->planName;
    }

    public function setPlanName(string $planName): void
    {
        $this->planName = $planName;
    }

    public function getPaymentSchedule(): ?string
    {
        return $this->paymentSchedule;
    }

    public function setPaymentSchedule(?string $paymentSchedule): void
    {
        $this->paymentSchedule = $paymentSchedule;
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(?int $seats): void
    {
        $this->seats = $seats;
    }

    public function getStatus(): SubscriptionStatus
    {
        return $this->status;
    }

    public function setStatus(SubscriptionStatus $status): void
    {
        $this->active = match ($status) {
            SubscriptionStatus::ACTIVE, SubscriptionStatus::TRIAL_ACTIVE, SubscriptionStatus::OVERDUE_PAYMENT_OPEN, SubscriptionStatus::PENDING_CANCEL => true,
            default => false,
        };

        $this->status = $status;
    }

    public function getSubscriptionPlan(): ?SubscriptionPlan
    {
        return $this->subscriptionPlan;
    }

    public function setSubscriptionPlan(?SubscriptionPlan $subscriptionPlan): void
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    public function getMainExternalReference(): ?string
    {
        return $this->mainExternalReference;
    }

    public function setMainExternalReference(?string $mainExternalReference): void
    {
        $this->mainExternalReference = $mainExternalReference;
    }

    public function getChildExternalReference(): ?string
    {
        return $this->childExternalReference;
    }

    public function setChildExternalReference(?string $childExternalReference): void
    {
        $this->childExternalReference = $childExternalReference;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): void
    {
        $this->price = $price;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeInterface $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    public function endAtEndOfPeriod(): void
    {
        $this->endedAt = clone $this->validUntil;
    }

    public function endNow(): void
    {
        $this->endedAt = new \DateTime('now');
        $this->validUntil = new \DateTime('now');
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getValidUntil(): ?\DateTime
    {
        return $this->validUntil;
    }

    public function setValidUntil(\DateTime $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function getMoneyAmount(): Money
    {
        return Money::ofMinor($this->amount, Currency::of($this->currency));
    }

    public function setMoneyAmount(?Money $money)
    {
        if (!$money) {
            return;
        }

        $this->amount = $money->getMinorAmount()->toInt();
        $this->currency = $money->getCurrency()->getCurrencyCode();
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function getMainExternalReferenceDetailsUrl(): ?string
    {
        return $this->mainExternalReferenceDetailsUrl;
    }

    public function setMainExternalReferenceDetailsUrl(?string $mainExternalReferenceDetailsUrl): void
    {
        $this->mainExternalReferenceDetailsUrl = $mainExternalReferenceDetailsUrl;
    }

    public function getStartOfCurrentPeriod(): ?\DateTimeInterface
    {
        return $this->startOfCurrentPeriod;
    }

    public function setStartOfCurrentPeriod(?\DateTimeInterface $startOfCurrentPeriod): void
    {
        $this->startOfCurrentPeriod = $startOfCurrentPeriod;
    }

    public function isHasTrial(): bool
    {
        return $this->hasTrial;
    }

    public function setHasTrial(bool $hasTrial): void
    {
        $this->hasTrial = $hasTrial;
    }

    public function getTrialLengthDays(): ?int
    {
        return $this->trialLengthDays;
    }

    public function setTrialLengthDays(?int $trialLengthDays): void
    {
        $this->trialLengthDays = $trialLengthDays;
    }

    public function getPaymentDetails(): ?PaymentCard
    {
        return $this->paymentDetails;
    }

    public function setPaymentDetails(?PaymentCard $paymentDetails): void
    {
        $this->paymentDetails = $paymentDetails;
    }

    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function setPayments(Collection $payments): void
    {
        $this->payments = $payments;
    }
}
