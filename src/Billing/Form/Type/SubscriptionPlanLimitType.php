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

namespace Parthenon\Billing\Form\Type;

use Parthenon\Billing\Entity\SubscriptionFeature;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionPlanLimitType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('choices')
            ->setDefaults([
                'data_class' => SubscriptionPlanLimit::class,
            ],
            );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('limit', NumberType::class)
            ->add('subscriptionFeature', ChoiceType::class, [
                'choices' => $options['choices'],
                'choice_value' => 'id',
                'choice_label' => function (?SubscriptionFeature $subscriptionFeature) {
                    return $subscriptionFeature->getName();
                },
            ]);
    }
}
