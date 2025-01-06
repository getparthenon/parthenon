<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\Billing\Dto;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class StartSubscriptionDto
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SerializedName('plan_name')]
    private $planName;

    #[Assert\NotBlank]
    #[SerializedName('schedule')]
    #[Assert\Type('string')]
    private $schedule;

    #[SerializedName('seat_numbers')]
    private $seatNumbers = 1;

    #[SerializedName('currency')]
    #[Assert\Currency]
    private $currency = 'usd';

    #[SerializedName('payment_details')]
    private $paymentDetailsId;

    public function getPlanName(): string
    {
        return $this->planName;
    }

    public function setPlanName(string $planName): void
    {
        $this->planName = $planName;
    }

    public function getSeatNumbers(): int
    {
        return $this->seatNumbers;
    }

    public function setSeatNumbers(int $seatNumbers): void
    {
        $this->seatNumbers = $seatNumbers;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function setSchedule(string $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getPaymentDetailsId(): ?string
    {
        return $this->paymentDetailsId;
    }

    /**
     * @param null $paymentDetailsId
     */
    public function setPaymentDetailsId($paymentDetailsId): void
    {
        $this->paymentDetailsId = $paymentDetailsId;
    }

    public function hasPaymentDetailsId(): bool
    {
        return isset($this->paymentDetailsId);
    }
}
