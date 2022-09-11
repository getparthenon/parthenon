<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\DependencyInjection\Modules;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void;

    public function handleDefaultParameters(ContainerBuilder $container): void;

    public function handleConfiguration(array $config, ContainerBuilder $container): void;
}
