<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Compiler;

use Parthenon\Common\Compiler\AbstractCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RuleEngineCompilerPass extends AbstractCompilerPass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('parthenon.rule_engine.repository_manager')) {
            return;
        }

        $this->handle($container, 'parthenon.rule_engine.repository_manager', 'parthenon.rule_engine.repository', 'addRepository');
    }
}
