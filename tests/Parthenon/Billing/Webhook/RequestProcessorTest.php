<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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
