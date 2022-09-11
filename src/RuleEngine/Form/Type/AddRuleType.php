<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Form\Type;

use Parthenon\RuleEngine\Entity\Rule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddRuleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['entityProperties', 'entities', 'actions', 'default_entity']);
        $resolver->setDefaults([
            'data_class' => Rule::class,
            'allow_extra_fields' => true,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['entityProperties'] as $entityName => $properties) {
            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($properties, $entityName) {
                $rule = $event->getData();
                $form = $event->getForm();

                if (!$rule || $rule['entity'] !== $entityName) {
                    return;
                }

                $form->remove('field')
                    ->add('field', ChoiceType::class, [
                        'label' => 'parthenon.rule_engine.add.form.field',
                        'choices' => array_combine($properties, $properties),
                    ]);
            });
        }

        $fields = [];
        if (isset($options['default_entity'])) {
            $entityName = $options['default_entity'];
            $fields = $options['entityProperties'][$entityName];
        }

        $builder->add('entity', ChoiceType::class, [
            'label' => 'parthenon.rule_engine.add.form.entity',
            'choices' => array_combine($options['entities'], $options['entities']),
            'placeholder' => 'Choose an option',
        ])
            ->add('field', ChoiceType::class, ['label' => 'parthenon.rule_engine.add.form.field', 'choices' => $fields])
            ->add('action', ChoiceType::class, [
                'label' => 'parthenon.rule_engine.add.form.action',
                'choices' => $options['actions'],
                'placeholder' => 'Choose an option',
            ])
            ->add('value', TextType::class, ['label' => 'parthenon.rule_engine.add.form.value'])
            ->add('options', HiddenType::class)
            ->add('comparison', ChoiceType::class, [
                'label' => 'parthenon.rule_engine.add.form.comparison',
                'choices' => [
                    'equal' => '==',
                    'identical' => '===',
                    'not equal' => '!=',
                    'not identical' => '!==',
                    'less than' => '<',
                    'greater than' => '>',
                    'less than or equal to' => '<=',
                    'greater than or equal to' => '>=',
                    'regex' => 'matches',
                ],
            ]);
    }
}
