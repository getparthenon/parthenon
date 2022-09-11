<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\RequestHandler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class FormRequestHandlerTest extends TestCase
{
    public function testSupportsNonuserRoute()
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('form');
        $request->method('get')->willReturn('parthenon_user_view');

        $requestHandler = new FormRequestHandler();
        $this->assertTrue($requestHandler->supports($request));
    }

    public function testDoesNotSupportsNonuserRoute()
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('form');
        $request->method('get')->willReturn('parthenon_user_signup');

        $requestHandler = new FormRequestHandler();
        $this->assertFalse($requestHandler->supports($request));
    }

    public function testCallsFormCreateViewDefault()
    {
        $form = $this->createMock(Form::class);
        $formView = $this->createMock(FormView::class);

        $form->method('createView')->willReturn($formView);

        $requestHandler = new FormRequestHandler();
        $this->assertEquals(['form' => $formView], $requestHandler->generateDefaultOutput($form));
    }

    public function testCallsFormCreateViewError()
    {
        $form = $this->createMock(Form::class);
        $formView = $this->createMock(FormView::class);

        $form->method('createView')->willReturn($formView);

        $requestHandler = new FormRequestHandler();
        $this->assertEquals(['form' => $formView], $requestHandler->generateErrorOutput($form));
    }

    public function testCallsFormCreateViewSucces()
    {
        $form = $this->createMock(Form::class);
        $formView = $this->createMock(FormView::class);

        $form->method('createView')->willReturn($formView);

        $requestHandler = new FormRequestHandler();
        $this->assertEquals(['form' => $formView], $requestHandler->generateSuccessOutput($form));
    }
}
