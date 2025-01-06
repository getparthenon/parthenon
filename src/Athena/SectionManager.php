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

namespace Parthenon\Athena;

use Parthenon\Athena\Controller\AthenaControllerInterface;
use Parthenon\Athena\Exception\NoSectionFoundException;

final class SectionManager implements SectionManagerInterface
{
    /**
     * @var SectionInterface[]
     */
    private array $sections = [];
    /**
     * @var AthenaControllerInterface[]
     */
    private array $controllers = [];

    public function __construct(private AccessRightsManagerInterface $accessRightsManager)
    {
    }

    public function addSection(SectionInterface $section): self
    {
        $this->sections[] = $section;

        return $this;
    }

    public function addController(AthenaControllerInterface $backofficeController): self
    {
        $this->controllers[] = $backofficeController;

        return $this;
    }

    /**
     * @return SectionInterface[]
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * @return AthenaControllerInterface[]
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * @throws NoSectionFoundException
     */
    public function getByUrlTag(string $urlTag): SectionInterface
    {
        foreach ($this->sections as $section) {
            if ($section->getUrlTag() === $urlTag) {
                return $section;
            }
        }
        throw new NoSectionFoundException(sprintf("No section found for url tag '%s'", $urlTag));
    }

    /**
     * @throws NoSectionFoundException
     */
    public function getByEntity(mixed $entity): SectionInterface
    {
        if (!is_object($entity)) {
            throw new \InvalidArgumentException('Expected object');
        }

        foreach ($this->sections as $section) {
            $rawEntity = $section->getEntity();
            $rawEntityClass = get_class($rawEntity);
            if (is_a($entity, $rawEntityClass)) {
                return $section;
            }
        }

        throw new NoSectionFoundException(sprintf('No section found for entity of class "%s"', get_class($entity)));
    }

    public function getMenu(): array
    {
        $output = [];

        foreach ($this->sections as $section) {
            $urlTag = $section->getUrlTag();
            $sectionName = $section->getMenuSection();
            $menuName = $section->getMenuName();

            if (!array_key_exists($sectionName, $output)) {
                $output[$sectionName] = [
                    'items' => [],
                    'roles' => [],
                ];
            }
            $sectionRights = $this->accessRightsManager->getAccessRights($section);

            $output[$sectionName]['items'][$menuName] = [
                'route' => 'parthenon_athena_crud_'.$urlTag.'_list',
                'required_role' => $sectionRights['view'] ?? 'USER_ROLE',
            ];
            $output[$sectionName]['roles'][] = $sectionRights['view'] ?? 'USER_ROLE';
        }

        foreach ($this->controllers as $controller) {
            foreach ($controller->getMenuOptions() as $sectionName => $menuOptions) {
                if (!array_key_exists($sectionName, $output)) {
                    $output[$sectionName] = [
                        'items' => [],
                        'roles' => [],
                    ];
                }

                foreach ($menuOptions as $menuName => $info) {
                    $output[$sectionName]['items'][$menuName] = [
                        'route' => $info['route'],
                        'required_role' => $info['role'] ?? 'ROLE_USER',
                    ];
                    $output[$sectionName]['roles'][] = $info['role'] ?? 'ROLE_USER';
                }
            }
        }

        foreach ($output as $key => $item) {
            $output[$key]['roles'] = array_unique($item['roles']);
        }

        return $output;
    }
}
