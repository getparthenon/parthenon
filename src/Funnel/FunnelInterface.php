<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Funnel;

use Parthenon\Common\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

interface FunnelInterface
{
    public function setEntity($entity): FunnelInterface;

    public function addStep(StepInterface $step): self;

    public function setRepository(RepositoryInterface $repository): self;

    public function setSuccessHandler(SuccessHandlerInterface $successHandler): self;

    public function setSkipHandler(SkipHandlerInterface $skipHandler): self;

    public function process(Request $request);
}
