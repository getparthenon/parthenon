<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\DependencyInjection\Modules;

use Parthenon\Common\Exception\NonExistentClass;
use Parthenon\Common\Exception\ParameterNotSetException;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Gdpr\Deletion\DeleterInterface;
use Parthenon\User\Gdpr\Deletion\VoterInterface;
use Parthenon\User\Gdpr\Export\ExporterInterface;
use Parthenon\User\Gdpr\Export\FormatterInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class User implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('user')
                ->children()
                    ->booleanNode('enabled')->defaultValue(false)->end()
                    ->scalarNode('user_class')->end()
                    ->booleanNode('email_confirmation')->defaultValue(true)->end()
                    ->booleanNode('signed_in_after_signup')->defaultValue(false)->end()
                    ->booleanNode('user_invites_enabled')->defaultValue(false)->end()
                    ->scalarNode('login_route')->defaultValue('parthenon_user_login')->end()
                    ->scalarNode('login_redirect_route')->defaultValue('parthenon_user_profile')->end()
                    ->scalarNode('signup_success_route')->defaultValue('parthenon_user_signed_up')->end()
                    ->booleanNode('teams_enabled')->defaultValue(false)->end()
                    ->booleanNode('teams_invites_enabled')->defaultValue(false)->end()
                    ->booleanNode('self_signup_enabled')->defaultValue(true)->end()
                    ->scalarNode('team_class')->end()
                    ->scalarNode('firewall_name')->end()
                    ->arrayNode('roles')
                        ->children()
                            ->scalarNode('default_role')->defaultValue('ROLE_USER')->end()
                            ->arrayNode('user_assignable')
                                 ->useAttributeAsKey('name')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('athena_assignable')
                                ->useAttributeAsKey('name')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('gdpr')
                        ->children()
                            ->arrayNode('export')
                                ->children()
                                ->scalarNode('export_format')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon_user_login_route', 'parthenon_user_login');
        $container->setParameter('parthenon_user_signup_success_route', 'parthenon_user_signed_up');
        $container->setParameter('parthenon_user_users_invites_enabled', false);
        $container->setParameter('parthenon_user_teams_enabled', false);
        $container->setParameter('parthenon_user_team_class', null);
        $container->setParameter('parthenon_user_teams_invites_enabled', false);
        $container->setParameter('parthenon_user_gdpr_formatter_type', 'json');
        $container->setParameter('parthenon_user_roles_default_role', 'ROLE_USER');
        $container->setParameter('parthenon_user_roles_user_assignable_roles', []);
        $container->setParameter('parthenon_user_roles_athena_assignable_roles', []);
        $container->setParameter('parthenon_user_self_signup_enabled', true);
        $container->setParameter('parthenon_user_email_confirmation', true);
        $container->setParameter('parthenon_user_signed_in_after_signup', false);
        $container->setParameter('parthenon_user_firewall_name', 'main');
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['user']) || !isset($config['user']['enabled']) || false == $config['user']['enabled']) {
            return;
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $bundles = $container->getParameter('kernel.bundles');

        $this->configureMongoDb($bundles, $loader);
        $this->configureDoctrine($bundles, $loader);

        $loader->load('services/user.xml');

        $this->configureAutotagging($container);
        $config = $this->configureUserClass($config, $container);
        $config = $this->configureSignupSuccessRoute($config, $container);
        $config = $this->configureLoginRoute($config, $container);
        $config = $this->configureUserInvitesEnabled($config, $container);
        $config = $this->configureTeamsInviteEnabled($config, $container);
        $config = $this->configureGdprFormatterType($config, $container);
        $config = $this->configureRoles($config, $container);
        $config = $this->configureSelfSignup($config, $container);
        $config = $this->configureEmailConfirmation($config, $container);
        $config = $this->configureSignedInAfterSignup($config, $container);
        $config = $this->configureFirewall($config, $container);

        $this->configureTeams($config, $container);
    }

    /**
     * @throws NonExistentClass
     * @throws ParameterNotSetException
     */
    private function configureTeams(array $config, ContainerBuilder $container): void
    {
        if (isset($config['user']['teams_enabled']) && $config['user']['teams_enabled']) {
            $container->setParameter('parthenon_user_teams_enabled', $config['user']['teams_enabled']);
            if (!isset($config['user']['team_class']) || empty($config['user']['team_class'])) {
                throw new ParameterNotSetException('When the user module is enabled and teams are enabled the team_class must be defined');
            }

            if (!class_exists($config['user']['team_class'])) {
                throw new NonExistentClass(sprintf("The class '%s' does not exist.", $config['user']['team_class']));
            }

            $teamDdfinition = $container->getDefinition(TeamInterface::class);
            $teamDdfinition->setClass($config['user']['team_class']);
            $container->setDefinition(TeamInterface::class, $teamDdfinition);
        }
    }

    private function configureGdprFormatterType(array $config, ContainerBuilder $container): array
    {
        if (isset($config['user']['gdpr']['export']['formatter_type']) && !empty($config['user']['gdpr']['export']['formatter_type'])) {
            $container->setParameter('parthenon_user_gdpr_formatter_type', $config['user']['gdpr']['export']['formatter_type']);
        }

        return $config;
    }

    private function configureTeamsInviteEnabled(array $config, ContainerBuilder $container): array
    {
        if (isset($config['user']['teams_invites_enabled'])) {
            $container->setParameter('parthenon_user_teams_invites_enabled', $config['user']['teams_invites_enabled']);
        }

        return $config;
    }

    private function configureUserInvitesEnabled(array $config, ContainerBuilder $container): array
    {
        if (isset($config['user']['user_invites_enabled'])) {
            $container->setParameter('parthenon_user_users_invites_enabled', $config['user']['user_invites_enabled']);
        }

        return $config;
    }

    private function configureRoles(array $config, ContainerBuilder $containerBuilder): array
    {
        if (!isset($config['user']['roles'])) {
            return $config;
        }

        $containerBuilder->setParameter('parthenon_user_roles_default_role', $config['user']['roles']['default_role'] ?? 'ROLE_USER');
        $containerBuilder->setParameter('parthenon_user_roles_user_assignable_roles', $config['user']['roles']['user_assignable'] ?? []);
        $containerBuilder->setParameter('parthenon_user_roles_athena_assignable_roles', $config['user']['roles']['athena_assignable'] ?? []);

        return $config;
    }

    private function configureLoginRoute(array $config, ContainerBuilder $container): array
    {
        if (isset($config['user']['login_route'])) {
            $container->setParameter('parthenon_user_login_route', $config['user']['login_route']);
        }

        return $config;
    }

    private function configureSignupSuccessRoute(array $config, ContainerBuilder $container): array
    {
        if (isset($config['user']['signup_success_route'])) {
            $container->setParameter('parthenon_user_signup_success_route', $config['user']['signup_success_route']);
        }

        return $config;
    }

    private function configureEmailConfirmation(array $config, ContainerBuilder $container): array
    {
        if (isset($config['user']['email_confirmation'])) {
            $container->setParameter('parthenon_user_email_confirmation', $config['user']['email_confirmation']);
        }

        return $config;
    }

    private function configureFirewall(array $config, ContainerBuilder $container): array
    {
        if (isset($config['user']['firewall_name'])) {
            $container->setParameter('parthenon_user_firewall_name', $config['user']['firewall_name']);
        }

        return $config;
    }

    private function configureSignedInAfterSignup(array $config, ContainerBuilder $container): array
    {
        if (isset($config['user']['signed_in_after_signup'])) {
            $container->setParameter('parthenon_user_signed_in_after_signup', $config['user']['signed_in_after_signup']);
        }

        return $config;
    }

    private function configureSelfSignup(array $config, ContainerBuilder $container): array
    {
        if (isset($config['user']['self_signup_enabled'])) {
            $container->setParameter('parthenon_user_self_signup_enabled', $config['user']['self_signup_enabled']);
        }

        return $config;
    }

    private function configureAutotagging(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(ExporterInterface::class)->addTag('parthenon.user.gdpr.export.exporter');
        $container->registerForAutoconfiguration(FormatterInterface::class)->addTag('parthenon.user.gdpr.export.formatter');
        $container->registerForAutoconfiguration(DeleterInterface::class)->addTag('parthenon.user.gdpr.delete.deleter');
        $container->registerForAutoconfiguration(VoterInterface::class)->addTag('parthenon.user.gdpr.delete.voter');
    }

    /**
     * @throws \Exception
     */
    private function configureDoctrine(float|array|bool|int|string|null $bundles, XmlFileLoader $loader): string|int|bool|array|null|float
    {
        if (isset($bundles['DoctrineBundle'])) {
            $loader->load('services/orm/user.xml');
        }

        return $bundles;
    }

    /**
     * @throws \Exception
     */
    private function configureMongoDb(float|int|bool|array|string|null $bundles, XmlFileLoader $loader): void
    {
        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $loader->load('services/odm/user.xml');
        }
    }

    /**
     * @throws NonExistentClass
     * @throws ParameterNotSetException
     */
    private function configureUserClass(array $config, ContainerBuilder $container): array
    {
        if (!isset($config['user']['user_class']) || empty($config['user']['user_class'])) {
            throw new ParameterNotSetException('When the user module is enabled the user_class must be defined');
        }

        if (!class_exists($config['user']['user_class'])) {
            throw new NonExistentClass(sprintf("The class '%s' does not exist.", $config['user']['user_class']));
        }

        $userDefintion = $container->getDefinition(UserInterface::class);
        $userDefintion->setClass($config['user']['user_class']);
        $container->setDefinition(UserInterface::class, $userDefintion);

        return $config;
    }
}
