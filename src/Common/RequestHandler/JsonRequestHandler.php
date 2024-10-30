<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Common\RequestHandler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class JsonRequestHandler implements RequestHandlerInterface
{
    public function supports(Request $request): bool
    {
        return 'json' === $request->getContentTypeFormat();
    }

    public function handleForm(FormInterface $form, Request $request): void
    {
        $data = json_decode($request->getContent(), true);
        if (is_null($data)) {
            throw new \Exception('No JSON');
        }
        $form->submit($data);
    }

    public function generateDefaultOutput(?FormInterface $form, array $extraOutput = []): array|Response
    {
        $output = [];

        if ($form) {
            $output = $this->convert($form->createView());
        }

        return new JsonResponse(array_merge($extraOutput, ['form' => $output]));
    }

    public function generateSuccessOutput(?FormInterface $form, array $extraOutput = []): array|Response
    {
        return new JsonResponse(array_merge($extraOutput, ['success' => true]), Response::HTTP_ACCEPTED);
    }

    public function generateErrorOutput(?FormInterface $form, array $extraOutput = []): array|Response
    {
        $errors = [];
        if ($form) {
            foreach ($form->getErrors(true, true) as $error) {
                $fieldName = $error->getOrigin()->getName();
                if (!isset($errors[$fieldName])) {
                    $errors[$fieldName] = [];
                }
                $errors[$fieldName][] = $error->getMessage();
            }
        }

        return new JsonResponse(array_merge($extraOutput, ['success' => false, 'errors' => $errors]), Response::HTTP_BAD_REQUEST);
    }

    private function convert(FormView $formView): array
    {
        $output = [];

        /**
         * @var FormView $field
         */
        foreach ($formView->getIterator() as $name => $field) {
            if (!empty($field->children)) {
                foreach ($field->children as $childName => $child) {
                    $output[$childName] = $this->convert($child);
                }
            } else {
                $output[$name] = $field->vars['value'];
            }
        }

        return $output;
    }
}
