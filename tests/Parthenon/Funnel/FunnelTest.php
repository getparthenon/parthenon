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

namespace Parthenon\Funnel;

use Parthenon\Funnel\Exception\NoEntitySetException;
use Parthenon\Funnel\Exception\NoSkipHandlerException;
use Parthenon\Funnel\Exception\NoSuccessHandlerSetException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FunnelTest extends TestCase
{
    public function testThrowExceptions()
    {
        $this->expectException(NoEntitySetException::class);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(Request::class);

        $requestStack->method('getSession')->willReturn($session);

        $funnel = new Funnel($formFactory, $requestStack);

        $funnel->addStep(new class implements StepInterface {
            public function isComplete(Request $request, FormFactoryInterface $formFactory, $entity): bool
            {
                return true;
            }

            public function getOutput(Request $request, FormFactoryInterface $formFactory, $entity)
            {
                return [];
            }
        });
        $funnel->process($request);
    }

    public function testCallsElement()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(Request::class);
        $element = $this->createMock(StepInterface::class);

        $entity = new class {
            public function getId(): string
            {
                return bin2hex(random_bytes(8));
            }
        };
        $data = ['data' => 'true'];

        $element->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $element->expects($this->once())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(false);

        $requestStack->method('getSession')->willReturn($session);

        $funnel = new Funnel($formFactory, $requestStack);
        $actual = $funnel->setEntity($entity)->addStep($element)->process($request);

        $this->assertEquals($actual, $data);
    }

    public function testCallsElementNoSuccessHandler()
    {
        $this->expectException(NoSuccessHandlerSetException::class);
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(Request::class);
        $element = $this->createMock(StepInterface::class);

        $entity = new class {
            public function getId(): string
            {
                return bin2hex(random_bytes(8));
            }
        };
        $data = ['data' => 'true'];

        $element->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $element->expects($this->once())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $requestStack->method('getSession')->willReturn($session);

        $funnel = new Funnel($formFactory, $requestStack);
        $actual = $funnel->setEntity($entity)->addStep($element)->process($request);
    }

    public function testCallsElementSuccessHandlerCalled()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(Request::class);
        $element = $this->createMock(StepInterface::class);
        $successHandler = $this->createMock(SuccessHandlerInterface::class);

        $entity = new class {
            public function getId(): string
            {
                return bin2hex(random_bytes(8));
            }
        };
        $data = ['data' => 'true'];

        $element->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $element->expects($this->once())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $requestStack->method('getSession')->willReturn($session);

        $successHandler->expects($this->once())->method('handleSuccess')->with($entity);

        $funnel = new Funnel($formFactory, $requestStack);
        $funnel->setEntity($entity)->addStep($element)->setSuccessHandler($successHandler)->process($request);
    }

    public function testCallsElementSkipHandler()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(Request::class);
        $elementOne = $this->createMock(StepInterface::class);
        $elementTwo = $this->createMock(StepInterface::class);
        $skipHandler = $this->createMock(SkipHandlerInterface::class);

        $id = bin2hex(random_bytes(8));
        $entity = new class($id) {
            public function __construct(private string $id)
            {
            }

            public function getId(): string
            {
                return $this->id;
            }
        };
        $funnelState = new FunnelState();
        $funnelState->setStep(1)->setEntity($entity);
        $data = ['data' => 'true'];

        $requestStack->method('getSession')->willReturn($session);

        $session->method('get')->with($id.'_funnel')->willReturn($funnelState);
        $session->method('set')->with($id.'_funnel', $funnelState);

        $elementOne->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $elementOne->expects($this->never())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $elementTwo->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $elementTwo->expects($this->never())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $skipHandler->expects($this->once())->method('handleSkip')->with($entity)->willReturn($data);

        $request->method('get')->withConsecutive(['clear'], ['skip'])->willReturnOnConsecutiveCalls(null, 'true');

        $funnel = new Funnel($formFactory, $requestStack);
        $funnel->setEntity($entity)->addStep($elementOne)->addStep($elementTwo)->setSkipHandler($skipHandler);
        $this->assertEquals($data, $funnel->process($request));
    }

    public function testNoSkipHandler()
    {
        $this->expectException(NoSkipHandlerException::class);
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(Request::class);
        $elementOne = $this->createMock(StepInterface::class);
        $elementTwo = $this->createMock(StepInterface::class);
        $skipHandler = $this->createMock(SkipHandlerInterface::class);

        $id = bin2hex(random_bytes(8));
        $entity = new class($id) {
            public function __construct(private string $id)
            {
            }

            public function getId(): string
            {
                return $this->id;
            }
        };
        $funnelState = new FunnelState();
        $funnelState->setStep(1)->setEntity($entity);
        $data = ['data' => 'true'];

        $requestStack->method('getSession')->willReturn($session);

        $session->method('get')->with($id.'_funnel')->willReturn($funnelState);
        $session->method('set')->with($id.'_funnel', $funnelState);

        $elementOne->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $elementOne->expects($this->never())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $elementTwo->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $elementTwo->expects($this->never())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $request->method('get')->withConsecutive(['clear'], ['skip'])->willReturnOnConsecutiveCalls(null, 'true');

        $funnel = new Funnel($formFactory, $requestStack);
        $funnel->setEntity($entity)->addStep($elementOne)->addStep($elementTwo);
        $this->assertEquals($data, $funnel->process($request));
    }

    public function testCallsCorrectElements()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(Request::class);
        $elementOne = $this->createMock(StepInterface::class);
        $elementTwo = $this->createMock(StepInterface::class);
        $skipHandler = $this->createMock(SkipHandlerInterface::class);

        $id = bin2hex(random_bytes(8));
        $entity = new class($id) {
            public function __construct(private string $id)
            {
            }

            public function getId(): string
            {
                return $this->id;
            }
        };
        $funnelState = new FunnelState();
        $funnelState->setStep(1)->setEntity($entity);
        $data = ['data' => 'true'];

        $requestStack->method('getSession')->willReturn($session);

        $session->method('get')->with($id.'_funnel')->willReturn($funnelState);

        $session->method('set')->with($id.'_funnel', $funnelState);

        $elementOne->expects($this->never())->method('getOutput')->with($request, $formFactory, $entity)->willReturn([]);
        $elementOne->expects($this->never())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $elementTwo->expects($this->once())->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $elementTwo->expects($this->once())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(false);

        $funnel = new Funnel($formFactory, $requestStack);
        $funnel->setEntity($entity)->addStep($elementOne)->addStep($elementTwo);
        $this->assertEquals($data, $funnel->process($request));
    }

    public function testCallsCOrrectElementsIncrementsStep()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(Request::class);
        $elementOne = $this->createMock(StepInterface::class);
        $elementTwo = $this->createMock(StepInterface::class);
        $skipHandler = $this->createMock(SkipHandlerInterface::class);

        $id = bin2hex(random_bytes(8));
        $entity = new class($id) {
            public function __construct(private string $id)
            {
            }

            public function getId(): string
            {
                return $this->id;
            }
        };
        $funnelState = new FunnelState();
        $funnelState->setStep(
            0
        )->setEntity($entity);
        $data = ['data' => 'true'];

        $requestStack->method('getSession')->willReturn($session);

        $session->method('get')->with($id.'_funnel')->willReturn($funnelState);
        $session->method('set')->with($id.'_funnel', $this->callback(function (FunnelState $funnelState) {
            return 1 === $funnelState->getStep();
        }));

        $elementOne->expects($this->never())->method('getOutput')->with($request, $formFactory, $entity)->willReturn([]);
        $elementOne->expects($this->once())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $elementTwo->expects($this->once())->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $elementTwo->expects($this->never())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(false);

        $funnel = new Funnel($formFactory, $requestStack);
        $funnel->setEntity($entity)->addStep($elementOne)->addStep($elementTwo);
        $this->assertEquals($data, $funnel->process($request));
    }
}
