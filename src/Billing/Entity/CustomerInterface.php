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

use Parthenon\Athena\Entity\CrudEntityInterface;
use Parthenon\Common\Address;

interface CustomerInterface extends CrudEntityInterface
{
    public function getId();

    public function hasSubscription(): bool;

    public function hasActiveSubscription(): bool;

    /**
     * @throw NoSubscriptionException
     */
    public function getSubscription(): Subscription;

    public function setSubscription(Subscription $subscription);

    public function setBillingAddress(Address $address);

    public function getBillingAddress(): Address;

    public function hasBillingAddress(): bool;

    public function setExternalCustomerReference($externalCustomerReference);

    public function getExternalCustomerReference();

    public function getBillingEmail();

    public function getPaymentProviderDetailsUrl();
}
