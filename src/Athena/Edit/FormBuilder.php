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

namespace Parthenon\Athena\Edit;

use Parthenon\Athena\EntityForm;
use Parthenon\Athena\Exception\InvalidFormTypeException;
use Parthenon\Athena\Form\Type\AthenaType;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\FieldAccesorTrait;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

final class FormBuilder
{
    use FieldAccesorTrait;
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function buildForm(EntityForm $entityForm, $entity, bool $edit = false)
    {
        $formBuilder = $this->formFactory->createBuilder(AthenaType::class, $entity);
        $this->handleForm($formBuilder, $entityForm->getFields(), $entity, $edit);

        return $formBuilder->getForm();
    }

    private function handleForm(FormBuilderInterface $formBuilder, array $fields, $entity, bool $edit = false): void
    {
        $subForms = [];
        foreach ($fields as $field) {
            if ($edit && !$field->isEditable()) {
                continue;
            }

            if ($field->hasSubEntity()) {
                $subName = $field->getSubName();
                $subField = $field->getSubField();

                if (!isset($subForms[$subName])) {
                    $subForms[$subName] = [];
                }
                $subForms[$subName][] = $subField;

                continue;
            }
            $formBuilder->add($field->getName(), $this->getTypeClassName($field->getType()), $field->getExtraOptions());
        }

        foreach ($subForms as $formName => $fields) {
            $subEntity = $this->getFieldData($entity, $formName);
            if (!is_object($subEntity)) {
                throw new GeneralException('Invalid form field');
            }
            $formBuilder->add($formName, FormType::class, ['data_class' => get_class($subEntity)]);
            $formBuilder->get($formName)->setData($subEntity);

            $this->handleForm($formBuilder->get($formName), $fields, $subEntity);
        }
    }

    private function getTypeClassName(string $type): string
    {
        if (class_exists($type)) {
            return $type;
        }

        $typeName = str_replace(' ', '', ucwords(str_replace('_', ' ', $type)));
        $typeClass = '\\Symfony\\Component\\Form\\Extension\\Core\\Type\\'.$typeName.'Type';

        if (class_exists($typeClass)) {
            return $typeClass;
        }

        throw new InvalidFormTypeException('There is no form type for '.$type);
    }
}
