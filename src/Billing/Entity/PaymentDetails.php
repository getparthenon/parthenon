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

class PaymentDetails
{
    protected $id;

    protected $customer;

    protected string $provider;

    protected string $storedCustomerReference;

    protected string $storedPaymentReference;

    protected string $name;

    protected bool $default = true;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    public function getStoredCustomerReference(): string
    {
        return $this->storedCustomerReference;
    }

    public function setStoredCustomerReference(string $storedCustomerReference): void
    {
        $this->storedCustomerReference = $storedCustomerReference;
    }
}
