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

namespace Parthenon\MultiTenancy\Validator;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueSubdomainValidator extends ConstraintValidator
{
    public function __construct(private TenantRepositoryInterface $tenantRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueSubdomain) {
            throw new UnexpectedTypeException($constraint, UniqueSubdomain::class);
        }

        try {
            $this->tenantRepository->findBySubdomain($value);
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(UniqueSubdomain::IS_NOT_UNIQUE_USER)
                ->addViolation();
        } catch (NoEntityFoundException $e) {
        }
    }
}
