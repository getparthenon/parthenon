<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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

    /**
     * {@inheritdoc}
     */
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
