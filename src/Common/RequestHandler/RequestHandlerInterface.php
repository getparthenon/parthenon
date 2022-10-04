<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common\RequestHandler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface RequestHandlerInterface
{
    public function supports(Request $request): bool;

    public function handleForm(FormInterface $form, Request $request): void;

    public function generateDefaultOutput(?FormInterface $form, array $extraOutput = []): array|Response;

    public function generateSuccessOutput(?FormInterface $form, array $extraOutput = []): array|Response;

    public function generateErrorOutput(?FormInterface $form, array $extraOutput = []): array|Response;
}
