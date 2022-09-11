<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Controller;

use Parthenon\MultiTenancy\RequestProcessor\TenantSignup;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController
{
    #[Route('/tenant/signup', name: 'parthenon_multi_tenancy_signup')]
    public function tenantSignup(Request $request, TenantSignup $tenantSignup)
    {
        return $tenantSignup->process($request);
    }
}
