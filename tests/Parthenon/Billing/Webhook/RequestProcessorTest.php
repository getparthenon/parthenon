<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Webhook;

use Monolog\Test\TestCase;
use Obol\Model\Events\EventInterface;
use Obol\Model\WebhookPayload;
use Obol\Provider\ProviderInterface;
use Obol\WebhookServiceInterface;
use Parthenon\Billing\Config\WebhookConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ServerBag;

class RequestProcessorTest extends TestCase
{
    public function testProcessor()
    {
        $event = $this->createMock(EventInterface::class);

        $provider = $this->createMock(ProviderInterface::class);
        $webhookService = $this->createMock(WebhookServiceInterface::class);
        $provider->method('webhook')->willReturn($webhookService);
        $webhookService->expects($this->once())->method('process')->with($this->isInstanceOf(WebhookPayload::class))->willReturn($event);

        $manager = $this->createMock(HandlerManagerInterface::class);
        $manager->expects($this->once())->method('handle')->with($event);
        $webhookConfig = new WebhookConfig('secret_config');

        $request = $this->createMock(Request::class);
        $serverBag = $this->createMock(ServerBag::class);
        $request->server = $serverBag;
        $request->method('getContent')->willReturn(json_encode([]));
        $serverBag->method('get')->with('HTTP_STRIPE_SIGNATURE')->willReturn('siganture');

        $subject = new RequestProcessor($webhookConfig, $provider, $manager);
        $subject->processRequest($request);
    }

    public function testProcessorNull()
    {
        $event = $this->createMock(EventInterface::class);

        $provider = $this->createMock(ProviderInterface::class);
        $webhookService = $this->createMock(WebhookServiceInterface::class);
        $provider->method('webhook')->willReturn($webhookService);
        $webhookService->expects($this->once())->method('process')->with($this->isInstanceOf(WebhookPayload::class))->willReturn(null);

        $manager = $this->createMock(HandlerManagerInterface::class);
        $manager->expects($this->never())->method('handle')->with($event);
        $webhookConfig = new WebhookConfig('secret_config');

        $request = $this->createMock(Request::class);
        $serverBag = $this->createMock(ServerBag::class);
        $request->server = $serverBag;
        $request->method('getContent')->willReturn(json_encode([]));
        $serverBag->method('get')->with('HTTP_STRIPE_SIGNATURE')->willReturn('siganture');

        $subject = new RequestProcessor($webhookConfig, $provider, $manager);
        $subject->processRequest($request);
    }
}
