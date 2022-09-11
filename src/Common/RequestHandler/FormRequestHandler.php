<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
