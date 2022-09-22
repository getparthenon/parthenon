<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Subscriber;

use Parthenon\Payments\Entity\Subscription;

interface SubscriberInterface
{
    public const TYPE_TEAM = 'team';

    public const TYPE_USER = 'user';

    public function setSubscription(Subscription $subscription);

    public function getSubscription(): ?Subscription;

    public function hasActiveSubscription(): bool;

    public function getIdentifier(): string;
}
