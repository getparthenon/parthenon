<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Webhook;

use Obol\Model\WebhookPayload;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Config\WebhookConfig;
use Symfony\Component\HttpFoundation\Request;

final class RequestProcessor implements RequestProcessorInterface
{
    public function __construct(
        private WebhookConfig $config,
        private ProviderInterface $provider,
        private HandlerManagerInterface $handlerManager,
    ) {
    }

    public function processRequest(Request $request): void
    {
        $webhookPayload = new WebhookPayload();
        $webhookPayload->setPayload($request->getContent());
        $webhookPayload->setSignature($request->get('stripe-signature'));
        $webhookPayload->setSecret($this->config->getSecret());

        $event = $this->provider->webhook()->process($webhookPayload);

        if (!$event) {
            return;
        }

        $this->handlerManager->handle($event);
    }
}
