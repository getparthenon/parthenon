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

namespace Parthenon\Payments\Entity;

use Parthenon\Payments\SubscriptionInterface;

class Subscription implements SubscriptionInterface
{
    public const STATUS_UNKNOWN = 'unknown';
    public const STATUS_PENDING = 'pending';
    public const STATUS_TRIAL = 'trial';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_ARRAY = [
        self::STATUS_UNKNOWN,
        self::STATUS_PENDING,
        self::STATUS_TRIAL,
        self::STATUS_ACTIVE,
        self::STATUS_CANCELLED,
        self::STATUS_OVERDUE,
    ];

    public const PAYMENT_SCHEDULE_MONTHLY = 'monthly';
    public const PAYMENT_SCHEDULE_YEARLY = 'yearly';
    public const PAYMENT_SCHEDULE_LIFETIME = 'lifetime';

    private ?string $priceId;

    private ?string $planName = '';

    private ?string $paymentSchedule;

    private ?bool $active;

    private ?string $customerId = null;

    private ?string $status = self::STATUS_UNKNOWN;

    private ?\DateTimeInterface $startedAt;

    private ?\DateTimeInterface $validUntil;

    private ?\DateTimeInterface $endedAt;

    private ?\DateTimeInterface $updatedAt;

    private ?string $paymentId;

    private ?string $checkoutId;

    private ?int $seats;

    public function getPriceId(): ?string
    {
        return $this->priceId;
    }

    public function setPriceId(string $priceId): void
    {
        $this->priceId = $priceId;
    }

    public function getPlanName(): ?string
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

    public function setPaymentSchedule(string $paymentSchedule): void
    {
        $this->paymentSchedule = $paymentSchedule;
    }

    public function isActive(): bool
    {
        if (self::PAYMENT_SCHEDULE_LIFETIME === $this->paymentSchedule) {
            return true === $this->active;
        }

        $now = new \DateTime();

        return true === $this->active && $this->validUntil > $now;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    public function getValidUntil(): ?\DateTimeInterface
    {
        return $this->validUntil;
    }

    public function setValidUntil(?\DateTimeInterface $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeInterface $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    public function getCheckoutId(): ?string
    {
        return $this->checkoutId;
    }

    public function setCheckoutId(string $checkoutId): void
    {
        $this->checkoutId = $checkoutId;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function increaseValidUntil()
    {
        if (self::PAYMENT_SCHEDULE_YEARLY === $this->paymentSchedule) {
            $this->validUntil = new \DateTime('+1 Year');
        } elseif (self::PAYMENT_SCHEDULE_MONTHLY === $this->paymentSchedule) {
            $this->validUntil = new \DateTime('+1 month');
        }
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(int $seats): void
    {
        $this->seats = $seats;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): void
    {
        $this->customerId = $customerId;
    }
}
