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

namespace Parthenon\MultiTenancy\RequestProcessor;

use Parthenon\Common\Config\SiteUrlProviderInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\RequestHandler\RequestHandlerManagerInterface;
use Parthenon\MultiTenancy\Creator\TenantCreatorInterface;
use Parthenon\MultiTenancy\Database\DatabaseSwitcherInterface;
use Parthenon\MultiTenancy\Event\PostTenantSignupEvent;
use Parthenon\MultiTenancy\Event\PreTenantSignupEvent;
use Parthenon\MultiTenancy\Factory\TenantFactoryInterface;
use Parthenon\MultiTenancy\Factory\UserFactoryInterface;
use Parthenon\MultiTenancy\Form\Type\SignupType;
use Parthenon\MultiTenancy\Model\SignUp;
use Parthenon\MultiTenancy\TenantProvider\SimpleTenantProvider;
use Parthenon\MultiTenancy\TenantProvider\TenantProviderAwareInterface;
use Parthenon\User\Creator\UserCreatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class TenantSignup
{
    use LoggerAwareTrait;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private RequestHandlerManagerInterface $requestHandlerManager,
        private SignupType $signupType,
        private EventDispatcherInterface $dispatcher,
        private TenantFactoryInterface $tenantFactory,
        private UserFactoryInterface $userFactory,
        private TenantCreatorInterface $tenantCreator,
        private UserCreatorInterface $userCreator,
        private DatabaseSwitcherInterface $databaseSwitcher,
        private SiteUrlProviderInterface|TenantProviderAwareInterface $siteUrlProvider,
    ) {
    }

    public function process(Request $request)
    {
        $formType = $this->formFactory->create(get_class($this->signupType));
        try {
            $requestHandler = $this->requestHandlerManager->getRequestHandler($request);

            if ($request->isMethod('POST')) {
                $requestHandler->handleForm($formType, $request);
                if ($formType->isSubmitted() && $formType->isValid()) {
                    /** @var SignUp $signUp */
                    $signUp = $formType->getData();
                    $tenant = $this->tenantFactory->buildTenantFromSignUp($signUp);
                    $this->dispatcher->dispatch(new PreTenantSignupEvent($tenant), PreTenantSignupEvent::NAME);

                    $this->tenantCreator->createTenant($tenant);
                    $this->databaseSwitcher->switchToTenant($tenant);
                    $this->siteUrlProvider->setTenantProvider(new SimpleTenantProvider($tenant));
                    $user = $this->userFactory->buildUserFromSignUp($signUp);
                    $this->userCreator->create($user);
                    $this->dispatcher->dispatch(new PostTenantSignupEvent($tenant), PostTenantSignupEvent::NAME);

                    return $requestHandler->generateSuccessOutput($formType);
                }

                return $requestHandler->generateErrorOutput($formType);
            }

            return $requestHandler->generateDefaultOutput($formType);
        } catch (\Throwable $e) {
            $this->getLogger()->error('Error handling sign up', ['exception_message' => $e->getMessage()]);

            return $requestHandler->generateErrorOutput($formType);
        }
    }
}
