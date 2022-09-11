<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2021, all rights reserved.
 */

namespace Parthenon;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Parthenon\AbTesting\Compiler\AbTestingCompilerPass;
use Doctrine\DBAL\Types\Type;
use Parthenon\Athena\Compiler\AthenaCompilerPass;
use Parthenon\Common\Compiler\CommonCompilerPass;
use Parthenon\Funnel\Compiler\FunnelCompilerPass;
use Parthenon\Health\Compiler\HealthCompilerPass;
use Parthenon\MultiTenancy\Compiler\MultiTenancyCompilerPass;
use Parthenon\Plan\Compiler\PlanCompilerPass;
use Parthenon\RuleEngine\Compiler\RuleActionsCompilerPass;
use Parthenon\RuleEngine\Compiler\RuleEngineCompilerPass;
use Parthenon\Subscriptions\CompilerPass\SubscriptionsCompilerPass;
use Parthenon\User\CompilerPass\UserCompilerPass;
use Parthenon\User\Dbal\Types\UtcDateTimeType;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ParthenonBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $mappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping/User') => 'Parthenon\User\Entity',
            realpath(__DIR__.'/Resources/config/doctrine-mapping/Athena') => 'Parthenon\Athena\Entity',
            realpath(__DIR__.'/Resources/config/doctrine-mapping/Common') => 'Parthenon\Common',
            realpath(__DIR__.'/Resources/config/doctrine-mapping/Subscriptions') => 'Parthenon\Subscriptions\Entity',
            realpath(__DIR__.'/Resources/config/doctrine-mapping/AbTesting') => 'Parthenon\AbTesting\Entity',
            realpath(__DIR__.'/Resources/config/doctrine-mapping/MultiTenancy') => 'Parthenon\MultiTenancy\Entity',
        ];

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, ['parthenon.orm'], ));
            Type::overrideType('datetime', UtcDateTimeType::class);
            Type::overrideType('datetimetz', UtcDateTimeType::class);
        }

        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, ['parthenon.mongodb']));
        }

        $container->addCompilerPass(new CommonCompilerPass());
        $container->addCompilerPass(new AbTestingCompilerPass());
        $container->addCompilerPass(new AthenaCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $container->addCompilerPass(new FunnelCompilerPass());
        $container->addCompilerPass(new HealthCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $container->addCompilerPass(new SubscriptionsCompilerPass());
        $container->addCompilerPass(new UserCompilerPass());
        $container->addCompilerPass(new MultiTenancyCompilerPass());
    }
}
