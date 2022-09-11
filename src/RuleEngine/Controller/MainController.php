<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Controller;

use Parthenon\Athena\Controller\AthenaControllerInterface;
use Parthenon\RuleEngine\Action\ActionManager;
use Parthenon\RuleEngine\Entity\Rule;
use Parthenon\RuleEngine\Form\Type\AddRuleType;
use Parthenon\RuleEngine\Repository\RuleRepositoryInterface;
use Parthenon\RuleEngine\RepositoryManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class MainController implements AthenaControllerInterface
{
    public function getMenuOptions(): array
    {
        return [
            'Rule Engine' => [
                'Configure' => [
                    'route' => 'parthenon_rule_athena_index',
                    'action' => 'configure',
                ],
            ],
        ];
    }

    #[Route('/athena/rule-engine', name: 'parthenon_rule_athena_index')]
    #[Template('@Parthenon/athena/rule_engine/configure.html.twig')]
    public function configure(RepositoryManager $repositoryManager): array
    {
        $entities = [];

        foreach ($repositoryManager->getRepositories() as $repository) {
            $entity = $repository->getEntity();

            if (!$entity || !is_object($entity)) {
                throw new \Exception('Invalid entity returned');
            }
            $entities[] = get_class($entity);
        }

        return [
            'entities' => $entities,
        ];
    }

    #[Route('/athena/rule-engine/list/{entity}', name: 'parthenon_rule_athena_list')]
    #[Template('@Parthenon/athena/rule_engine/list.html.twig')]
    public function listRules(Request $request, RuleRepositoryInterface $ruleEngineRepository)
    {
        $entityName = $request->get('entity');
        $entites = $ruleEngineRepository->getAllRulesForEntity($entityName);

        return ['entities' => $entites];
    }

    #[Route('/athena/rule-engine/add', name: 'parthenon_rule_athena_add')]
    #[Template('@Parthenon/athena/rule_engine/add.html.twig')]
    public function add(RepositoryManager $repositoryManager, ActionManager $actionManager, FormFactoryInterface $formFactory, RuleRepositoryInterface $ruleRepository, Request $request)
    {
        [$entities, $entityProperties] = $repositoryManager->getEntityInfo();
        [$actions, $actionOptions] = $actionManager->getActionInfo();

        $form = $formFactory->create(AddRuleType::class, new Rule(), ['actions' => $actions, 'entityProperties' => $entityProperties, 'entities' => $entities, 'default_entity' => $request->get('entity')]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $rule = $form->getData();
                $ruleRepository->save($rule);

                return new RedirectResponse('/backoffice/rule-engine');
            }
        }

        return ['form' => $form->createView(), 'entityProperties' => $entityProperties, 'actionOptions' => $actionOptions];
    }
}
