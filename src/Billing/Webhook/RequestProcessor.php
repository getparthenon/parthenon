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
        $webhookPayload->setSignature($request->server->get('HTTP_STRIPE_SIGNATURE'));
        $webhookPayload->setSecret($this->config->getSecret());

        $event = $this->provider->webhook()->process($webhookPayload);

        if (!$event) {
            return;
        }

        $this->handlerManager->handle($event);
    }
}
