<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common\RequestHandler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormRequestHandler implements RequestHandlerInterface
{
    public function supports(Request $request): bool
    {
        return (null === $request->getContentType() || 'form' === $request->getContentType()) && 'parthenon_user_signup' !== $request->get('_route');
    }

    public function handleForm(FormInterface $form, Request $request): void
    {
        $form->handleRequest($request);
    }

    public function generateDefaultOutput(?FormInterface $form, array $extraOutput = []): array|Response
    {
        if ($form) {
            $extraOutput['form'] = $form->createView();
        }

        return $extraOutput;
    }

    public function generateSuccessOutput(?FormInterface $form, array $extraOutput = []): array|Response
    {
        if ($form) {
            $extraOutput['form'] = $form->createView();
        }

        return $extraOutput;
    }

    public function generateErrorOutput(?FormInterface $form, array $extraOutput = []): array|Response
    {
        if ($form) {
            $extraOutput['form'] = $form->createView();
        }

        return $extraOutput;
    }
}
