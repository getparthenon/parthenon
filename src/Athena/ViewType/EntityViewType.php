<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
