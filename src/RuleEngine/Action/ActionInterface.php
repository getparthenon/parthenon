<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Action;

interface ActionInterface
{
    public function getName(): string;

    public function getOptions(): array;

    public function execute(array $options, $entity): void;
}
