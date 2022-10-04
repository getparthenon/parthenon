<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

        $funnel->addStep(new class() implements StepInterface {
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

        $entity = new class() {
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

        $entity = new class() {
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

        $entity = new class() {
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

        $entity = new class() {
        };
        $funnelState = new FunnelState();
        $funnelState->setStep(1)->setEntity($entity);
        $data = ['data' => 'true'];

        $requestStack->method('getSession')->willReturn($session);

        $session->method('get')->with(get_class($entity).'_funnel')->willReturn($funnelState);
        $session->method('set')->with(get_class($entity).'_funnel', $funnelState)->willReturn(null);

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

        $entity = new class() {
        };
        $funnelState = new FunnelState();
        $funnelState->setStep(1)->setEntity($entity);
        $data = ['data' => 'true'];

        $requestStack->method('getSession')->willReturn($session);

        $session->method('get')->with(get_class($entity).'_funnel')->willReturn($funnelState);
        $session->method('set')->with(get_class($entity).'_funnel', $funnelState)->willReturn(null);

        $elementOne->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $elementOne->expects($this->never())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $elementTwo->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $elementTwo->expects($this->never())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $request->method('get')->withConsecutive(['clear'], ['skip'])->willReturnOnConsecutiveCalls(null, 'true');

        $funnel = new Funnel($formFactory, $requestStack);
        $funnel->setEntity($entity)->addStep($elementOne)->addStep($elementTwo);
        $this->assertEquals($data, $funnel->process($request));
    }

    public function testCallsCOrrectElements()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(Request::class);
        $elementOne = $this->createMock(StepInterface::class);
        $elementTwo = $this->createMock(StepInterface::class);
        $skipHandler = $this->createMock(SkipHandlerInterface::class);

        $entity = new class() {
        };
        $funnelState = new FunnelState();
        $funnelState->setStep(1)->setEntity($entity);
        $data = ['data' => 'true'];

        $requestStack->method('getSession')->willReturn($session);

        $session->method('get')->with(get_class($entity).'_funnel')->willReturn($funnelState);

        $session->method('set')->with(get_class($entity).'_funnel', $funnelState)->willReturn(null);

        /*   $session->method('set')->with(get_class($entity).'_funnel', $this->callback(function (FunnelState $funnelState) {
               return 1 === $funnelState->getStep();
           }))->willReturn(null);
   */
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

        $entity = new class() {
        };
        $funnelState = new FunnelState();
        $funnelState->setStep(
            0
        )->setEntity($entity);
        $data = ['data' => 'true'];

        $requestStack->method('getSession')->willReturn($session);

        $session->method('get')->with(get_class($entity).'_funnel')->willReturn($funnelState);
        $session->method('set')->with(get_class($entity).'_funnel', $this->callback(function (FunnelState $funnelState) {
            return 1 === $funnelState->getStep();
        }))->willReturn(null);

        $elementOne->expects($this->never())->method('getOutput')->with($request, $formFactory, $entity)->willReturn([]);
        $elementOne->expects($this->once())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(true);

        $elementTwo->expects($this->once())->method('getOutput')->with($request, $formFactory, $entity)->willReturn($data);
        $elementTwo->expects($this->never())->method('isComplete')->with($request, $formFactory, $entity)->willReturn(false);

        $funnel = new Funnel($formFactory, $requestStack);
        $funnel->setEntity($entity)->addStep($elementOne)->addStep($elementTwo);
        $this->assertEquals($data, $funnel->process($request));
    }
}
