<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\MultiTenancy\Form\Type;

use Parthenon\MultiTenancy\Model\SignUp;
use Parthenon\MultiTenancy\Validator\UniqueSubdomain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class SignupType extends AbstractType
{
    private SignUp $signUpModel;

    public function __construct(SignUp $signUpModel)
    {
        $this->signUpModel = $signUpModel;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => get_class($this->signUpModel),
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, ['label' => 'parthenon.multi_tenancy.form.signup.label.email'])
            ->add('password', PasswordType::class, ['label' => 'parthenon.multi_tenancy.form.signup.label.password'])
            ->add('subdomain', TextType::class, ['label' => 'parthenon.multi_tenancy.form.signup.label.subdomain', 'constraints' => [new UniqueSubdomain(), new Regex([
                'pattern' => '/^[a-z]+$/i',
                'htmlPattern' => '[a-zA-Z]+',
            ])]])
            ->add('name', TextType::class, ['label' => 'parthenon.multi_tenancy.form.signup.label.name']);
    }
}
