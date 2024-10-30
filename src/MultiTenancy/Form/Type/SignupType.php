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
