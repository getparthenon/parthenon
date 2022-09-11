<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Factory;

use Parthenon\MultiTenancy\Entity\TenantInterface;
use Parthenon\MultiTenancy\Model\SignUp;

interface TenantFactoryInterface
{
    public function buildTenantFromSignUp(SignUp $signUp): TenantInterface;
}
