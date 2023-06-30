<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Entity;

use Brick\Money\Currency;
use Brick\Money\Money;

class EmbeddedSubscription
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

    private ?string $planName = '';

    private ?string $paymentSchedule;

    private ?bool $active;

    private ?string $status = self::STATUS_UNKNOWN;

    private ?\DateTimeInterface $startedAt;

    private ?\DateTimeInterface $validUntil;

    private ?\DateTimeInterface $endedAt;

    private ?\DateTimeInterface $updatedAt;

    private ?int $seats;

    private int $amount;

    private string $currency;

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
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getMoneyAmount(): Money
    {
        return Money::ofMinor($this->amount, Currency::of($this->currency));
    }

    public function setMoneyAmount(Money $money)
    {
        $this->amount = $money->getAmount()->getUnscaledValue()->toInt();
        $this->currency = $money->getCurrency()->getCurrencyCode();
    }
}
