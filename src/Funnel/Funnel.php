<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Funnel;

use Parthenon\Common\Exception\NoRepositorySetException;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\Repository\RepositoryAwareInterface;
use Parthenon\Common\Repository\RepositoryInterface;
use Parthenon\Funnel\Exception\InvalidStepException;
use Parthenon\Funnel\Exception\NoEntitySetException;
use Parthenon\Funnel\Exception\NoSkipHandlerException;
use Parthenon\Funnel\Exception\NoSuccessHandlerSetException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class Funnel implements FunnelInterface
{
    use LoggerAwareTrait;

    /**
     * @var StepInterface[]
     */
    private array $steps = [];
    private $entity;
    private ?RepositoryInterface $repository;
    private FormFactoryInterface $formFactory;
    private SuccessHandlerInterface $successHandler;
    private SkipHandlerInterface $skipHandler;

    private SessionInterface $session;

    public function __construct(FormFactoryInterface $formFactory, RequestStack $requestStack)
    {
        $this->formFactory = $formFactory;
        $this->session = $requestStack->getSession();
    }

    public function setSkipHandler(SkipHandlerInterface $skipHandler): FunnelInterface
    {
        $this->skipHandler = $skipHandler;

        return $this;
    }

    public function setRepository(RepositoryInterface $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    public function setEntity($entity): FunnelInterface
    {
        $this->entity = $entity;

        return $this;
    }

    public function addStep(StepInterface $step): FunnelInterface
    {
        $this->steps[] = $step;

        return $this;
    }

    public function setSuccessHandler(SuccessHandlerInterface $successHandler): self
    {
        $this->successHandler = $successHandler;

        return $this;
    }

    public function process(Request $request)
    {
        if (!is_object($this->entity)) {
            $this->getLogger()->error('There is no error defined for funnel');
            throw new NoEntitySetException();
        }

        $newState = (null !== $request->get('clear', null) && $request->isMethod(Request::METHOD_GET));

        $funnelState = $this->getState($newState);

        $entity = $funnelState->getEntity();

        if (null !== $request->get('skip', null)) {
            return $this->handleSkip($entity);
        }

        $stepNumber = $funnelState->getStep();
        $step = $this->getStep($stepNumber);

        $this->getLogger()->info('Checking if step is complete', ['step' => $funnelState->getStep(), 'entity' => $this->getEntityName()]);
        if ($step->isComplete($request, $this->formFactory, $entity)) {
            ++$stepNumber;
            try {
                $step = $this->getStep($stepNumber);
                $funnelState->setStep($stepNumber);
            } catch (InvalidStepException $e) {
                $output = $this->handleSuccess($entity);
                $this->saveState($this->createState());

                return $output;
            }
        }

        $this->getLogger()->info('Getting output for step', ['step' => $funnelState->getStep(), 'entity' => $this->getEntityName()]);
        $output = $step->getOutput($request, $this->formFactory, $entity);
        $this->saveState($funnelState);

        return $output;
    }

    private function getStep(int $step): StepInterface
    {
        if (!isset($this->steps[$step])) {
            $this->getLogger()->error('Step not found.', ['step' => $step, 'entity' => $this->getEntityName()]);
            throw new InvalidStepException('Invalid step');
        }

        $step = $this->steps[$step];

        if ($step instanceof RepositoryAwareInterface) {
            if (null === $this->repository) {
                $this->getLogger()->error('There is no repository set to inject into step', ['entity' => $this->getEntityName()]);
                throw new NoRepositorySetException();
            }
            $step->setRepository($this->repository);
        }

        return $step;
    }

    private function handleSkip($entity)
    {
        if (!isset($this->skipHandler)) {
            $this->getLogger()->error('There is no skip handler set', ['entity' => $this->getEntityName()]);
            throw new NoSkipHandlerException();
        }

        if ($this->skipHandler instanceof RepositoryAwareInterface) {
            if (null === $this->repository) {
                $this->getLogger()->error('There is no repository set to inject into skip handler', ['entity' => $this->getEntityName()]);
                throw new NoRepositorySetException();
            }
            $this->skipHandler->setRepository($this->repository);
        }

        $this->getLogger()->info('Calling skip handler', ['entity' => $this->getEntityName()]);

        return $this->skipHandler->handleSkip($entity);
    }

    private function handleSuccess($entity)
    {
        if (!isset($this->successHandler)) {
            $this->getLogger()->error('There is no success handler set', ['entity' => $this->getEntityName()]);
            throw new NoSuccessHandlerSetException();
        }

        if ($this->successHandler instanceof RepositoryAwareInterface) {
            if (null === $this->repository) {
                $this->getLogger()->error('There is no repository set to inject into success handler', ['entity' => $this->getEntityName()]);
                throw new NoRepositorySetException();
            }
            $this->successHandler->setRepository($this->repository);
        }

        $this->getLogger()->info('Calling success handler', ['entity' => $this->getEntityName()]);

        return $this->successHandler->handleSuccess($entity);
    }

    private function getState(bool $newState): FunnelState
    {
        $this->getLogger()->info('Fetching funnel state from session', ['entity' => $this->getEntityName()]);
        $state = $this->session->get(get_class($this->entity).'_funnel');

        if (!$state instanceof FunnelState || $newState) {
            if ($newState) {
                $this->getLogger()->info('Clear flag sent. Creating a new funnel state.', ['entity' => $this->getEntityName()]);
            } else {
                $this->getLogger()->info('No state found. Creating new state', ['entity' => $this->getEntityName()]);
            }
            $state = $this->createState();
        }

        return $state;
    }

    private function saveState(FunnelState $funnelState)
    {
        $this->getLogger()->info('Saving funnel state', ['entity' => get_class($this->entity)]);
        $this->session->set(get_class($this->entity).'_funnel', $funnelState);
    }

    private function getEntityName(): string
    {
        static $className;
        if (!$className) {
            $className = get_class($this->entity);
        }

        return $className;
    }

    private function createState(): FunnelState
    {
        $state = new FunnelState();
        $state->setEntity($this->entity)
            ->setStep(0);

        return $state;
    }
}
