<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Doctrine\DBAL\Types\Type;
use Parthenon\AbTesting\Compiler\AbTestingCompilerPass;
use Parthenon\Athena\Compiler\AthenaCompilerPass;
use Parthenon\Common\Compiler\CommonCompilerPass;
use Parthenon\Funnel\Compiler\FunnelCompilerPass;
use Parthenon\Health\Compiler\HealthCompilerPass;
use Parthenon\MultiTenancy\Compiler\MultiTenancyCompilerPass;
use Parthenon\Payments\CompilerPass\SubscriptionsCompilerPass;
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
            realpath(__DIR__.'/Resources/config/doctrine-mapping/Payments') => 'Parthenon\Payments\Entity',
            realpath(__DIR__.'/Resources/config/doctrine-mapping/AbTesting') => 'Parthenon\AbTesting\Entity',
            realpath(__DIR__.'/Resources/config/doctrine-mapping/MultiTenancy') => 'Parthenon\MultiTenancy\Entity',
        ];

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, ['parthenon.orm']));
            Type::overrideType('datetime', UtcDateTimeType::class);
            Type::overrideType('datetimetz', UtcDateTimeType::class);
        }

        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, ['parthenon.mongodb']));
        }

        $container->addCompilerPass(new AbTestingCompilerPass());
        $container->addCompilerPass(new AthenaCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $container->addCompilerPass(new CommonCompilerPass());
        $container->addCompilerPass(new FunnelCompilerPass());
        $container->addCompilerPass(new HealthCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $container->addCompilerPass(new SubscriptionsCompilerPass());
        $container->addCompilerPass(new UserCompilerPass());
        $container->addCompilerPass(new MultiTenancyCompilerPass());
    }
}
