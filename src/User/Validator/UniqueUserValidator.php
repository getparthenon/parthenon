<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Validator;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueUserValidator extends ConstraintValidator
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueUser) {
            throw new UnexpectedTypeException($constraint, UniqueUser::class);
        }

        try {
            $this->userRepository->findByEmail($value);
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(UniqueUser::IS_NOT_UNIQUE_USER)
                ->addViolation();
        } catch (NoEntityFoundException $e) {
        }
    }
}
