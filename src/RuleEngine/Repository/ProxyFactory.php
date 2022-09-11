<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Repository;

use Parthenon\RuleEngine\Processor\ProcessorInterface;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory as Factory;

final class ProxyFactory
{
    private ProcessorInterface $instantProcessor;

    public function __construct(ProcessorInterface $instantProcessor)
    {
        $this->instantProcessor = $instantProcessor;
    }

    public function build(RuleEngineRepositoryInterface $ruleEngineRepository)
    {
        $factory = new Factory();
        $proxy = $factory->createProxy(
            $ruleEngineRepository,
            ['save' => function ($proxy, $instance, $method, $params) { $this->instantProcessor->process(current($params)); }],
            []
        );

        return $proxy;
    }
}
