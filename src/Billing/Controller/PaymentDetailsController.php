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

namespace Parthenon\Billing\Controller;

use Obol\Provider\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentDetailsController
{
    #[Route('/billing/card/add', name: 'parthenon_billing_paymentdetails_addcarddetails', methods: ['POST'])]
    public function addCardDetails(Request $request, ProviderInterface $provider)
    {
    }

    #[Route('/billing/card/token/add', name: 'parthenon_billing_paymentdetails_addcardbytoken', methods: ['POST'])]
    public function addCardByToken(Request $request, ProviderInterface $provider)
    {
    }
}
