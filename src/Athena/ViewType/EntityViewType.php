<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Athena\ViewType;

use Parthenon\Athena\Entity\CrudEntityInterface;
use Parthenon\Athena\SectionManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EntityViewType implements ViewTypeInterface
{
    private mixed $data;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private SectionManagerInterface $sectionManager,
    ) {
    }

    public function getName(): string
    {
        return 'entity';
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getHtmlOutput(): string
    {
        if (!$this->data instanceof CrudEntityInterface) {
            throw new \InvalidArgumentException('Invalid entity');
        }

        $section = $this->sectionManager->getByEntity($this->data);

        $url = $this->urlGenerator->generate('parthenon_athena_crud_'.$section->getUrlTag().'_read', ['id' => $this->data->getId()]);

        return sprintf('<a href="%s">%s</a>', $url, $this->data->getDisplayName());
    }
}
