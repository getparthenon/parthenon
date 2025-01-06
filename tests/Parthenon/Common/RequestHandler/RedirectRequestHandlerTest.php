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

namespace Parthenon\Common\RequestHandler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectRequestHandlerTest extends TestCase
{
    public function testSupportsNonuserRoute()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $request = $this->createMock(Request::class);
        $request->method('getContentTypeFormat')->willReturn('form');
        $request->method('get')->willReturn('parthenon_user_signup');

        $requestHandler = new RedirectRequestHandler($urlGenerator, 'path', 'parthenon_user_signup');
        $this->assertTrue($requestHandler->supports($request));
    }

    public function testDoesNotSupportsNonuserRoute()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $request = $this->createMock(Request::class);
        $request->method('getContentTypeFormat')->willReturn('form');
        $request->method('get')->willReturn('parthenon_user_view');

        $requestHandler = new RedirectRequestHandler($urlGenerator, 'path', 'parthenon_user_signup');
        $this->assertFalse($requestHandler->supports($request));
    }

    public function testCallsFormCreateViewDefault()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $form = $this->createMock(Form::class);
        $formView = $this->createMock(FormView::class);

        $form->method('createView')->willReturn($formView);

        $requestHandler = new RedirectRequestHandler($urlGenerator, 'path', 'parthenon_user_signup');
        $this->assertEquals(['form' => $formView], $requestHandler->generateDefaultOutput($form));
    }

    public function testCallsFormCreateViewError()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $form = $this->createMock(Form::class);
        $formView = $this->createMock(FormView::class);

        $form->method('createView')->willReturn($formView);

        $requestHandler = new RedirectRequestHandler($urlGenerator, 'path', 'parthenon_user_signup');
        $this->assertEquals(['form' => $formView], $requestHandler->generateErrorOutput($form));
    }

    public function testCallsFormCreateViewSucces()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate', 'path')->willReturn('route');

        $form = $this->createMock(Form::class);
        $formView = $this->createMock(FormView::class);

        $form->method('createView')->willReturn($formView);

        $requestHandler = new RedirectRequestHandler($urlGenerator, 'path', 'parthenon_user_signup');
        $this->assertInstanceOf(RedirectResponse::class, $requestHandler->generateSuccessOutput($form));
    }
}
