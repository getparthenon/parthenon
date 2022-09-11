<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
        return 'json' === $request->getContentType();
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
                $output[$name] = ['value' => $field->vars['value'], 'choices' => $field->vars['choices'] ?? []];
            }
        }

        return $output;
    }
}
