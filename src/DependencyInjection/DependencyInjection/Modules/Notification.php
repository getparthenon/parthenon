<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\DependencyInjection\Modules;

use Parthenon\Common\Exception\ParameterNotSetException;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\Notification\Sender\MailgunEmailSender;
use Parthenon\Notification\Sender\MessengerEmailSender;
use Parthenon\Notification\Sender\PostmarkEmailSender;
use Parthenon\Notification\Sender\SendGridEmailSender;
use Parthenon\Notification\Sender\SymfonyEmailSender;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class Notification implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('notification')
                ->children()
                    ->booleanNode('enabled')->end()
                    ->arrayNode('email')
                        ->children()
                            ->scalarNode('from_address')->defaultValue('parthenon@example.org')->end()
                            ->scalarNode('from_name')->defaultValue('Parthenon')->end()
                            ->scalarNode('provider')->end()
                            ->booleanNode('send_via_queue')->defaultValue(false)->end()
                            ->arrayNode('postmark')
                                ->children()
                                    ->scalarNode('api_key')->end()
                                ->end()
                            ->end()
                            ->arrayNode('mailgun')
                                ->children()
                                    ->scalarNode('api_key')->end()
                                    ->scalarNode('api_url')->end()
                                    ->scalarNode('domain')->end()
                                ->end()
                            ->end()
                            ->arrayNode('sendgrid')
                                ->children()
                                    ->scalarNode('api_key')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('slack')
                        ->children()
                            ->scalarNode('client_id')->end()
                            ->scalarNode('client_secret')->end()
                            ->scalarNode('redirect_url')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon_notification_email_from_address', 'parthenon@example.org');
        $container->setParameter('parthenon_notification_email_from_name', 'Parthenon');
        $container->setParameter('parthenon_notification_email_postmark_apikey', 'please-set-api-key');
        $container->setParameter('parthenon_notification_email_mailgun_apikey', 'please-set-api-key');
        $container->setParameter('parthenon_notification_email_mailgun_domain', 'please-set-domain');
        $container->setParameter('parthenon_notification_email_mailgun_api_url', 'https://api.mailgun.net');
        $container->setParameter('parthenon_notification_email_sendgrid_apikey', 'please-set-api-key');
        $container->setParameter('parthenon_notification_slack_client_id', '');
        $container->setParameter('parthenon_notification_slack_client_secret', '');
        $container->setParameter('parthenon_notification_slack_redirect_url', '');
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['notification']) || !isset($config['notification']['enabled']) || false == $config['notification']['enabled']) {
            return;
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('services/notification.xml');

        $config = $this->configureSlack($config, $container);

        if (!isset($config['notification']['email'])) {
            return;
        }

        $senderInterface = $this->configureSendToQueue($config['notification']['email']['send_via_queue'], $container);
        $config = $this->configureProvider($config, $container, $senderInterface);
        $config = $this->configureFromAddress($config, $container);
        $this->configureFromName($config, $container);
    }

    private function configureSlack(array $config, ContainerBuilder $container): array
    {
        if (isset($config['notification']['slack'])) {
            if (isset($config['notification']['slack']['client_id'])) {
                $container->setParameter('parthenon_notification_slack_client_id', $config['notification']['slack']['client_id']);
            }

            if (isset($config['notification']['slack']['client_secret'])) {
                $container->setParameter('parthenon_notification_slack_client_secret', $config['notification']['slack']['client_secret']);
            }

            if (isset($config['notification']['slack']['redirect_url'])) {
                $container->setParameter('parthenon_notification_slack_redirect_url', $config['notification']['slack']['redirect_url']);
            }
        }

        return $config;
    }

    /**
     * @param $send_via_queue
     */
    private function configureSendToQueue($send_via_queue, ContainerBuilder $container): string
    {
        if (true === $send_via_queue) {
            $senderInterface = 'parthenon.notification.sender.background';

            $container->setAlias(EmailSenderInterface::class, MessengerEmailSender::class);
        } else {
            $senderInterface = EmailSenderInterface::class;
        }

        return $senderInterface;
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configureMailGun(array $config, ContainerBuilder $container, string $senderInterface): array
    {
        if (!isset($config['notification']['email']['mailgun']) || empty($config['notification']['email']['mailgun'])) {
            throw new ParameterNotSetException('When the notification.email.provider is Mailgun you need to define mailgun.api_key and mailgun.domain');
        }
        if (!isset($config['notification']['email']['mailgun']['api_key']) || empty($config['notification']['email']['mailgun']['api_key'])) {
            throw new ParameterNotSetException('When the notification.email.provider is Mailgun you need to define mailgun.api_key');
        }
        if (!isset($config['notification']['email']['mailgun']['domain']) || empty($config['notification']['email']['mailgun']['domain'])) {
            throw new ParameterNotSetException('When the notification.email.provider is Mailgun you need to define mailgun.domain');
        }
        if (isset($config['notification']['email']['mailgun']['api_url']) && !empty($config['notification']['email']['mailgun']['api_url'])) {
            $container->setParameter('parthenon_notification_email_mailgun_api_url', $config['notification']['email']['mailgun']['api_url']);
        }

        $container->setParameter('parthenon_notification_email_mailgun_api_key', $config['notification']['email']['mailgun']['api_key']);
        $container->setParameter('parthenon_notification_email_mailgun_domain', $config['notification']['email']['mailgun']['domain']);

        $container->setAlias($senderInterface, MailgunEmailSender::class);

        return $config;
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configureSendgrid(array $config, ContainerBuilder $container, string $senderInterface): array
    {
        if (!isset($config['notification']['email']['sendgrid']) || empty($config['notification']['email']['sendgrid'])) {
            throw new ParameterNotSetException('When the notification.email.provider is SendGrid you need to define sendgrid.api_key');
        }

        if (!isset($config['notification']['email']['sendgrid']['api_key']) || empty($config['notification']['email']['sendgrid']['api_key'])) {
            throw new ParameterNotSetException('When the notification.email.provider is SendGrid you need to define sendgrid.api_key');
        }

        $container->setParameter('parthenon_notification_email_sendgrid_api_key', $config['notification']['email']['sendgrid']['api_key']);

        $container->setAlias($senderInterface, SendGridEmailSender::class);

        return $config;
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configurePostmark(array $config, ContainerBuilder $container, string $senderInterface): array
    {
        if (!isset($config['notification']['email']['postmark']) || empty($config['notification']['email']['postmark'])) {
            throw new ParameterNotSetException('When the notification.email.provider is Postmark you need to define postmark.api_key');
        }

        if (!isset($config['notification']['email']['postmark']['api_key']) || empty($config['notification']['email']['postmark']['api_key'])) {
            throw new ParameterNotSetException('When the notification.email.provider is Postmark you need to define postmark.api_key');
        }

        $container->setParameter('parthenon_notification_email_sendgrid_api_key', $config['notification']['email']['sendgrid']['api_key']);

        $container->setAlias($senderInterface, PostmarkEmailSender::class);

        return $config;
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configureProvider(array $config, ContainerBuilder $container, string $senderInterface): array
    {
        if (isset($config['notification']['email']['provider'])) {
            if ('mailgun' === strtolower($config['notification']['email']['provider'])) {
                $config = $this->configureMailGun($config, $container, $senderInterface);
            } elseif ('sendgrid' === strtolower($config['notification']['email']['provider'])) {
                $config = $this->configureSendgrid($config, $container, $senderInterface);
            } elseif ('postmark' === strtolower($config['notification']['email']['provider'])) {
                $config = $this->configurePostmark($config, $container, $senderInterface);
            } elseif ('symfony' === strtolower($config['notification']['email']['provider'])) {
                $container->setAlias($senderInterface, SymfonyEmailSender::class);
            }
        }

        return $config;
    }

    private function configureFromAddress(array $config, ContainerBuilder $container): array
    {
        if (isset($config['notification']['email']['from_address'])) {
            $container->setParameter('parthenon_notification_email_from_address', $config['notification']['email']['from_address']);
        }

        return $config;
    }

    private function configureFromName(array $config, ContainerBuilder $container): void
    {
        if (isset($config['notification']['email']['from_name'])) {
            $container->setParameter('parthenon_notification_email_from_name', $config['notification']['email']['from_name']);
        }
    }
}
