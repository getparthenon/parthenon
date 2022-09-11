<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Edit\Field;
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

    public function field(string $name, $type = 'text', array $extraOptions = []): self
    {
        if (is_null($this->openSection)) {
            throw new NoSectionOpenException();
        }

        $this->openSection->addField(new Field($name, $type, $extraOptions));

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
