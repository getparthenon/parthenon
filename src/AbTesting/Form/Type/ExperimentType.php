<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Form\Type;

use Parthenon\AbTesting\Entity\Experiment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperimentType extends AbstractType
{
    public const CSRF_TOKEN_ID = 'experiment_item';

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Experiment::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => self::CSRF_TOKEN_ID,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class, ['choices' => ['session' => 'session', 'user' => 'user']])
            ->add('name', TextType::class)
            ->add('desiredResult', TextType::class)
            ->add('variants', CollectionType::class,
                [
                    'entry_type' => VariantType::class,
                    'allow_add' => true,
                ]
            );
    }
}
