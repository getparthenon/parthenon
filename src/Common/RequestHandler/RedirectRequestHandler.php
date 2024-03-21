<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Common\RequestHandler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectRequestHandler implements RequestHandlerInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator, private string $urlPath, private string $route)
    {
    }

    public function supports(Request $request): bool
    {
        return (null === $request->getContentTypeFormat() || 'form' === $request->getContentTypeFormat()) && $this->route === $request->get('_route');
    }

    public function handleForm(FormInterface $form, Request $request, array $extraOutput = []): void
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
        return new RedirectResponse($this->urlGenerator->generate($this->urlPath));
    }

    public function generateErrorOutput(?FormInterface $form, array $extraOutput = []): array|Response
    {
        if ($form) {
            $extraOutput['form'] = $form->createView();
        }

        return $extraOutput;
    }
}
