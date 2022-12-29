<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
