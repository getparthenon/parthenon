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

namespace Parthenon\AbTesting\Controller;

use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\AbTesting\Entity\Variant;
use Parthenon\AbTesting\Form\Type\ExperimentType;
use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use Parthenon\Athena\Controller\AthenaControllerInterface;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AbController implements AthenaControllerInterface
{
    public function getMenuOptions(): array
    {
        return [
            'AB Tests' => [
                'Configure' => [
                    'route' => 'parthenon_athena_abtesting_list',
                    'action' => 'listAction',
                ],
            ],
        ];
    }

    #[Route('/athena/abtesting/list', name: 'parthenon_athena_abtesting_list')]
    #[Template('@Parthenon/athena/abtesting/experiment/list.html.twig')]
    public function listAction(ExperimentRepositoryInterface $repository, Request $request, LoggerInterface $logger)
    {
        $logger->info('A/B Experiments List page viewed');

        $limit = $request->get('limit', CrudRepositoryInterface::LIMIT);
        $sortKey = $request->get('sort_key', 'id');
        $lastKey = $request->get('last_key', null);
        $stepBackKey = $request->get('step_back_key', null);
        $sortType = strtoupper($request->get('sort_type', 'ASC'));

        $results = $repository->getList([], $sortKey, $sortType, $limit, $lastKey);

        return [
            'results' => $results,
            'stepBackKey' => $stepBackKey,
            'currentLastKey' => $lastKey,
            'currentSortKey' => $sortKey,
            'currentSortType' => $sortType,
        ];
    }

    #[Route('/athena/abtesting/create', name: 'parthenon_athena_abtesting_create')]
    #[Template('@Parthenon/athena/abtesting/experiment/create.html.twig')]
    public function createAction(ExperimentRepositoryInterface $repository, FormFactoryInterface $formFactory, Request $request, UrlGeneratorInterface $urlGenerator)
    {
        $form = $formFactory->create(ExperimentType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /** @var Experiment $entity */
                $entity = $form->getData();

                // Hacky fix for now.
                /** @var Variant $variant */
                foreach ($entity->getVariants() as $variant) {
                    $variant->setExperiment($entity);
                }

                $entity->setCreatedAt(new \DateTime());
                $repository->save($entity);

                return new RedirectResponse($urlGenerator->generate('parthenon_athena_abtesting_list'));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    #[Route('/athena/abtesting/view/{id}', name: 'parthenon_athena_abtesting_view')]
    #[Template('@Parthenon/athena/abtesting/experiment/view.html.twig')]
    public function viewAction(Request $request, ExperimentRepositoryInterface $experimentRepository)
    {
        $experiment = $experimentRepository->getById($request->get('id'));

        return ['experiment' => $experiment];
    }

    #[Route('/athena/abtesting/edit/{id}', name: 'parthenon_athena_abtesting_edit')]
    #[Template('@Parthenon/athena/abtesting/experiment/edit.html.twig')]
    public function editAction(Request $request, FormFactoryInterface $formFactory, ExperimentRepositoryInterface $experimentRepository, UrlGeneratorInterface $urlGenerator)
    {
        $experiment = $experimentRepository->getById($request->get('id'));
        $form = $formFactory->create(ExperimentType::class, $experiment);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /** @var Experiment $entity */
                $entity = $form->getData();

                // Hacky fix for now.
                /** @var Variant $variant */
                foreach ($entity->getVariants() as $variant) {
                    $variant->setExperiment($entity);
                }

                $entity->setCreatedAt(new \DateTime());
                $experimentRepository->save($entity);

                return new RedirectResponse($urlGenerator->generate('parthenon_athena_abtesting_list'));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
