<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Edit\Field as EditField;
use Parthenon\Athena\Edit\Section;
use Parthenon\Athena\Exception\NoSectionOpenException;
use Parthenon\Athena\Exception\SectionAlreadyOpenException;

final class EntityForm
{
    /**
     * @var Section[]
     */
    private array $sections = [];

    private ?Section $openSection = null;

    public function section(string $name): self
    {
        if (!is_null($this->openSection)) {
            throw new SectionAlreadyOpenException();
        }

        $this->sections[] = $this->openSection = new Section($name);

        return $this;
    }

    public function field(string $name, $type = 'text', array $extraOptions = [], bool $editable = true): self
    {
        if (is_null($this->openSection)) {
            throw new NoSectionOpenException();
        }

        $this->openSection->addField(new EditField($name, $type, $extraOptions, $editable));

        return $this;
    }

    public function end(): self
    {
        $this->openSection = null;

        return $this;
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    // TODO consider yield

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        $fields = [];

        foreach ($this->sections as $section) {
            $fields[] = $section->getFields();
        }

        return array_merge([], ...$fields);
    }
}
