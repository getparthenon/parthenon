<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Doctrine\DBAL\Types\Type;
use Parthenon\AbTesting\Compiler\AbTestingCompilerPass;
use Parthenon\Athena\Compiler\AthenaCompilerPass;
use Parthenon\Billing\Compiler\BillingCompilerPass;
use Parthenon\Common\Compiler\CommonCompilerPass;
use Parthenon\Export\Compiler\ExportCompilerPass;
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
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $mappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping/Common') => 'Parthenon\Common',
        ];

        $abMappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping/AbTesting') => 'Parthenon\AbTesting\Entity',
        ];

        $exportMappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping/Export') => 'Parthenon\Export\Entity',
        ];

        $multiTenacyMappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping/MultiTenancy') => 'Parthenon\MultiTenancy\Entity',
        ];

        $paymentMappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping/Payments') => 'Parthenon\Payments\Entity',
        ];

        $userMappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping/User') => 'Parthenon\User\Entity',
        ];

        $userTeamMappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping/UserTeam') => 'Parthenon\User\Entity',
        ];

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, ['parthenon.orm']));
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($abMappings, ['parthenon.ab_testing.orm'], enabledParameter: 'parthenon_abtesting_enabled'));
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($exportMappings, ['parthenon.export.orm'], enabledParameter: 'parthenon_export_enabled'));
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($multiTenacyMappings, ['parthenon.multi_tenancy.orm'], enabledParameter: 'parthenon_multi_tenancy_enabled'));
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($paymentMappings, ['parthenon.payments.orm'], enabledParameter: 'parthenon_payments_enabled'));
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($userMappings, ['parthenon.user.orm'], enabledParameter: 'parthenon_user_enabled'));

            Type::overrideType('datetime', UtcDateTimeType::class);
            Type::overrideType('datetimetz', UtcDateTimeType::class);
        }

        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, ['parthenon.mongodb']));
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($abMappings, ['parthenon.ab_testing.mongodb'], enabledParameter: 'parthenon_abtesting_enabled'));
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($exportMappings, ['parthenon.export.mongodb'], enabledParameter: 'parthenon_export_enabled'));
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($multiTenacyMappings, ['parthenon.multi_tenancy.mongodb'], enabledParameter: 'parthenon_multi_tenancy_enabled'));
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($paymentMappings, ['parthenon.payments.mongodb'], enabledParameter: 'parthenon_payments_enabled'));
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($userMappings, ['parthenon.user.mongodb'], enabledParameter: 'parthenon_user_enabled'));
        }

        $container->addCompilerPass(new AbTestingCompilerPass());
        $container->addCompilerPass(new AthenaCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $container->addCompilerPass(new BillingCompilerPass());
        $container->addCompilerPass(new CommonCompilerPass());
        $container->addCompilerPass(new ExportCompilerPass());
        $container->addCompilerPass(new FunnelCompilerPass());
        $container->addCompilerPass(new HealthCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $container->addCompilerPass(new SubscriptionsCompilerPass());
        $container->addCompilerPass(new UserCompilerPass());
        $container->addCompilerPass(new MultiTenancyCompilerPass());

        $this->handleAthenaDoctrine($container);
        $this->handleBillingDoctrine($container);
    }

    public function handleAthenaDoctrine(ContainerBuilder $containerBuilder): void
    {
        $athenaMappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping/Athena') => 'Parthenon\Athena\Entity',
        ];

        // This contains bundles that are loaded
        $bundles = $containerBuilder->getParameter('kernel.bundles');

        // Doctrine ORM Bundle
        if (isset($bundles['DoctrineBundle'])) {
            $containerBuilder->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($athenaMappings, ['parthenon.athena.orm'], enabledParameter: 'parthenon_athena_enabled'));
        }
        // Doctrine ODM Bundle
        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $containerBuilder->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($athenaMappings, ['parthenon.athena.mongodb'], enabledParameter: 'parthenon_athena_enabled'));
        }
    }

    public function handleBillingDoctrine(ContainerBuilder $containerBuilder): void
    {
        $mappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping/Billing') => 'Parthenon\Billing\Entity',
        ];

        // This contains bundles that are loaded
        $bundles = $containerBuilder->getParameter('kernel.bundles');

        // Doctrine ORM Bundle
        if (isset($bundles['DoctrineBundle'])) {
            $containerBuilder->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, ['parthenon.billing.orm'], enabledParameter: 'parthenon_billing_enabled'));
        }
        // Doctrine ODM Bundle
        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $containerBuilder->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, ['parthenon.billing.mongodb'], enabledParameter: 'parthenon_billing_enabled'));
        }
    }
}
