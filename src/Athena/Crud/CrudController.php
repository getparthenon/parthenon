<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Crud;

use Parthenon\Athena\AccessRightsManagerInterface;
use Parthenon\Athena\Edit\FormBuilder;
use Parthenon\Athena\EntityForm;
use Parthenon\Athena\Filters\FilterManager;
use Parthenon\Athena\Filters\ListFilters;
use Parthenon\Athena\ListView;
use Parthenon\Athena\ReadView;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Athena\SectionInterface;
use Parthenon\Athena\ViewTypeManager;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class CrudController
{
    public function __construct(private SectionInterface $section, private ViewTypeManager $viewTypeManager, private FilterManager $filterManager, private AccessRightsManagerInterface $accessRightsManager, private Security $security)
    {
    }

    /**
     * @Template("@Parthenon/athena/crud/list.html.twig")
     */
    public function showList(Request $request, LoggerInterface $logger, Session $session)
    {
        $rights = $this->accessRightsManager->getAccessRights($this->section);

        if (!$this->security->isGranted($rights['view'])) {
            $logger->warning('Access denied to view an Athena CRUD List page');

            throw new AccessDeniedException();
        }

        $logger->info('Athena CRUD List page viewed');

        $listView = $this->section->buildListView(new ListView($this->viewTypeManager));
        $listFilters = $this->section->buildFilters(new ListFilters($this->filterManager));
        $filterData = $request->get('filters', []);
        $settings = $this->section->getSettings();

        if ($settings->hasSavedFilters()) {
            $sessionKey = sprintf('crud_%s_filters', $this->section->getUrlTag());
            if ($session->has($sessionKey)) {
                $savedFilters = $session->get($sessionKey);
                $filterData = array_merge($savedFilters, $filterData);
            }
            $session->set($sessionKey, $filterData);
            $session->save();
        }

        $filters = $listFilters->getFilters($filterData);
        $limit = (int) $request->get('limit', CrudRepositoryInterface::LIMIT);
        $sortKey = $request->get('sort_key', 'id');
        $lastKey = $request->get('last_key', null);
        $stepBackKey = $request->get('step_back_key', null);
        $sortType = strtoupper($request->get('sort_type', 'ASC'));

        $repository = $this->section->getRepository();
        $results = $repository->getList($filters, $sortKey, $sortType, $limit, $lastKey);

        return [
            'section' => $this->section,
            'results' => $results,
            'listView' => $listView,
            'listFilters' => $listFilters,
            'stepBackKey' => $stepBackKey,
            'currentLastKey' => $lastKey,
            'currentSortKey' => $sortKey,
            'currentSortType' => $sortType,
            'settings' => $settings,
            'buttons' => $this->section->getButtons(),
            'entityType' => get_class($this->section->getEntity()),
            'rights' => $rights,
        ];
    }

    /**
     * @Template("@Parthenon/athena/crud/read.html.twig")
     */
    public function showRead(Request $request, LoggerInterface $logger)
    {
        $rights = $this->accessRightsManager->getAccessRights($this->section);

        if (!$this->security->isGranted($rights['view'])) {
            $logger->warning('Access denied to view an Athena read List page');

            throw new AccessDeniedException();
        }

        if (!$this->section->getSettings()->isReadEnabled()) {
            $logger->warning('Athena CRUD read page called when disabled');
            throw new BadRequestHttpException();
        }

        $logger->info('Athena CRUD read entity page viewed');

        $id = $request->get('id');
        $entity = $this->section->getRepository()->getById($id, true);
        $readView = new ReadView($this->viewTypeManager);
        $readView = $this->section->buildReadView($readView);

        return [
            'section' => $this->section,
            'entity' => $entity,
            'readView' => $readView,
            'settings' => $this->section->getSettings(),
            'buttons' => $this->section->getButtons(),
            'entityType' => get_class($this->section->getEntity()),
            'rights' => $this->accessRightsManager->getAccessRights($this->section),
        ];
    }

    /**
     * @Template("@Parthenon/athena/crud/edit.html.twig")
     */
    public function edit(Request $request, LoggerInterface $logger, FormBuilder $formBuilder)
    {
        $settings = $this->section->getSettings();

        $rights = $this->accessRightsManager->getAccessRights($this->section);
        if (!$this->security->isGranted($rights['edit'])) {
            $logger->warning('Access denied to view an Athena edit page');

            throw new AccessDeniedException();
        }

        if (!$settings->isEditEnabled()) {
            $logger->warning('Athena CRUD edit page called when disabled');
            throw new BadRequestHttpException();
        }

        $logger->info('Athena CRUD edit entity page viewed');
        $id = $request->get('id');
        $entity = $this->section->getRepository()->getById($id, true);
        $entityForm = new EntityForm();
        $entityForm = $this->section->buildEntityForm($entityForm);

        $form = $formBuilder->buildForm($entityForm, $entity);
        $process = false;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->section->preSave($entity);
                $this->section->getRepository()->save($entity);
                $this->section->postSave($entity);
                $process = true;
            }
        }

        return [
            'section' => $this->section,
            'entity' => $entity,
            'entityForm' => $entityForm,
            'process' => $process,
            'form' => $form->createView(),
            'settings' => $this->section->getSettings(),
            'entityType' => get_class($this->section->getEntity()),
            'rights' => $this->accessRightsManager->getAccessRights($this->section),
        ];
    }

    /**
     * @Template("@Parthenon/athena/crud/create.html.twig")
     */
    public function create(Request $request, LoggerInterface $logger, FormBuilder $formBuilder)
    {
        $rights = $this->accessRightsManager->getAccessRights($this->section);
        if (!$this->security->isGranted($rights['create'])) {
            $logger->warning('Access denied to view an Athena create page');

            throw new AccessDeniedException();
        }

        $settings = $this->section->getSettings();
        if (!$settings->isCreateEnabled()) {
            $logger->warning('Athena CRUD create page called when disabled');
            throw new BadRequestHttpException();
        }

        $logger->info('Athena CRUD create entity page viewed');
        $entity = $this->section->getEntity();

        if (!is_object($entity)) {
            throw new \LogicException('Entity is not a valid object');
        }

        $entityForm = new EntityForm();
        $entityForm = $this->section->buildEntityForm($entityForm);

        $form = $formBuilder->buildForm($entityForm, $entity);
        $process = false;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if (method_exists($entity, 'setCreatedAt')) {
                    $entity->setCreatedAt(new \DateTime('now'));
                }
                $this->section->preSave($entity);
                $this->section->getRepository()->save($entity);
                $this->section->postSave($entity);

                $process = true;
                $form = $formBuilder->buildForm($entityForm, $this->section->getEntity());
            }
        }

        return [
            'section' => $this->section,
            'entity' => $entity,
            'entityForm' => $entityForm,
            'process' => $process,
            'form' => $form->createView(),
            'settings' => $this->section->getSettings(),
            'entityType' => get_class($this->section->getEntity()),
            'rights' => $this->accessRightsManager->getAccessRights($this->section),
        ];
    }

    public function delete(Request $request, LoggerInterface $logger, UrlGeneratorInterface $urlGenerator)
    {
        $rights = $this->accessRightsManager->getAccessRights($this->section);
        if (!$this->security->isGranted($rights['delete'])) {
            $logger->warning('Access denied to view an Athena delete page');

            throw new AccessDeniedException();
        }

        $settings = $this->section->getSettings();
        if (!$settings->isDeleteEnabled()) {
            $logger->warning('Athena CRUD delete page called when disabled');
            throw new BadRequestHttpException();
        }

        $logger->info('Athena CRUD delete entity page viewed');
        $id = $request->get('id');
        $entity = $this->section->getRepository()->getById($id, true);

        $this->section->getRepository()->delete($entity);

        return new RedirectResponse($urlGenerator->generate('parthenon_athena_crud_'.$this->section->getUrlTag().'_read', ['id' => $id]));
    }

    public function undelete(Request $request, LoggerInterface $logger, UrlGeneratorInterface $urlGenerator)
    {
        $rights = $this->accessRightsManager->getAccessRights($this->section);
        if (!$this->security->isGranted($rights['delete'])) {
            $logger->warning('Access denied to view an Athena delete page');

            throw new AccessDeniedException();
        }

        $settings = $this->section->getSettings();
        if (!$settings->isUndeleteEnabled()) {
            $logger->warning('Athena CRUD undelete page called when disabled');
            throw new BadRequestHttpException();
        }

        $logger->info('Athena CRUD undelete entity page viewed');
        $id = $request->get('id');
        $entity = $this->section->getRepository()->getById($id, true);

        $this->section->getRepository()->undelete($entity);

        return new RedirectResponse($urlGenerator->generate('parthenon_athena_crud_'.$this->section->getUrlTag().'_read', ['id' => $id]));
    }
}
