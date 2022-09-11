<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Factory;

use Parthenon\MultiTenancy\Entity\Tenant;
use Parthenon\MultiTenancy\Model\SignUp;
use PHPUnit\Framework\TestCase;

class TenantFactoryTest extends TestCase
{
    public function testReturnsTenant()
    {
        $factory = new TenantFactory(new Tenant());
        $signup = new SignUp();
        $signup->setSubdomain('mytestface');
        $tenant = $factory->buildTenantFromSignUp($signup);
        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertEquals('mytestface', $tenant->getSubdomain());
    }
}
