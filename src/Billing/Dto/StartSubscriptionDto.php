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
    private $paymentDetailsId = null;

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
