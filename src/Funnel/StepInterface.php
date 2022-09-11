<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Funnel;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

interface StepInterface
{
    public function isComplete(Request $request, FormFactoryInterface $formFactory, $entity): bool;

    public function getOutput(Request $request, FormFactoryInterface $formFactory, $entity);
}
