<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($abMappings, enabledParameter: 'parthenon_abtesting_enabled'));
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($exportMappings, enabledParameter: 'parthenon_export_enabled'));
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($multiTenacyMappings, ['doctrine.global_entity_manager'], enabledParameter: 'parthenon_multi_tenancy_enabled'));
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($paymentMappings, enabledParameter: 'parthenon_payments_enabled'));
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($userMappings, enabledParameter: 'parthenon_user_enabled'));

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
            $containerBuilder->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($athenaMappings, enabledParameter: 'parthenon_athena_enabled'));
        }
        // Doctrine ODM Bundle
        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $containerBuilder->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($athenaMappings, [], enabledParameter: 'parthenon_athena_enabled'));
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
            $containerBuilder->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, enabledParameter: 'parthenon_billing_enabled'));
        }
        // Doctrine ODM Bundle
        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $containerBuilder->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, [], enabledParameter: 'parthenon_billing_enabled'));
        }
    }
}
