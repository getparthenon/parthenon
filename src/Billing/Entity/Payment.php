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

use Brick\Money\Currency;
use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Payment
{
    protected ?string $paymentProviderDetailsUrl;
    private $id;

    private string $paymentReference;

    private string $provider;

    private int $amount;

    private string $currency;

    private CustomerInterface $customer;

    private \DateTimeInterface $createdAt;

    private \DateTimeInterface $updatedAt;

    private bool $refunded = false;

    private bool $completed = false;

    private bool $chargedBack = false;

    private Collection $subscriptions;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

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

    public function getPaymentReference(): string
    {
        return $this->paymentReference;
    }

    public function setPaymentReference(string $paymentReference): void
    {
        $this->paymentReference = $paymentReference;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
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

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function isRefunded(): bool
    {
        return $this->refunded;
    }

    public function setRefunded(bool $refunded): void
    {
        $this->refunded = $refunded;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function isChargedBack(): bool
    {
        return $this->chargedBack;
    }

    public function setChargedBack(bool $chargedBack): void
    {
        $this->chargedBack = $chargedBack;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
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

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getPaymentProviderDetailsUrl(): ?string
    {
        return $this->paymentProviderDetailsUrl;
    }

    public function setPaymentProviderDetailsUrl(?string $paymentProviderDetailsUrl): void
    {
        $this->paymentProviderDetailsUrl = $paymentProviderDetailsUrl;
    }

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(Collection|array $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function addSubscription(Subscription $subscription): void
    {
        $this->subscriptions->add($subscription);
    }
}
