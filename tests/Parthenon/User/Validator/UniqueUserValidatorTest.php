<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\User\Validator;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\User;
use Parthenon\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UniqueUserValidatorTest extends TestCase
{
    public function testUnique()
    {
        $userRepostiory = $this->createMock(UserRepositoryInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);

        $userRepostiory->method('findByEmail')->with($this->equalTo('email@example.org'))->will($this->throwException(new NoEntityFoundException()));

        $context->expects($this->never())->method('buildViolation');

        $validator = new UniqueUserValidator($userRepostiory);
        $validator->initialize($context);
        $validator->validate('email@example.org', new UniqueUser());
    }

    public function testNotValid()
    {
        $userRepostiory = $this->createMock(UserRepositoryInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $userRepostiory->method('findByEmail')->with($this->equalTo('email@example.org'))->will($this->returnValue(new User()));

        $constraint = new UniqueUser();
        $context->expects($this->once())->method('buildViolation')->with($this->equalTo($constraint->message))->will($this->returnValue($violationBuilder));

        $violationBuilder->expects($this->once())->method('setParameter')->will($this->returnSelf());
        $violationBuilder->expects($this->once())->method('setCode')->with($this->anything())->will($this->returnSelf());
        $violationBuilder->expects($this->once())->method('addViolation');

        $validator = new UniqueUserValidator($userRepostiory);
        $validator->initialize($context);
        $validator->validate('email@example.org', $constraint);
    }
}
