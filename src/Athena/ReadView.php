<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Exception\NoSectionOpenException;
use Parthenon\Athena\Exception\SectionAlreadyOpenException;
use Parthenon\Athena\Read\Section;

final class ReadView
{
    private ViewTypeManagerInterface $viewTypeManager;

    /**
     * @var Section[]
     */
    private array $sections = [];

    private ?Section $openSection = null;

    public function __construct(ViewTypeManagerInterface $viewTypeManager)
    {
        $this->viewTypeManager = $viewTypeManager;
    }

    public function section(string $name, ?string $controller = null): self
    {
        if (!is_null($this->openSection)) {
            throw new SectionAlreadyOpenException();
        }

        $this->sections[] = $this->openSection = new Section($name, $controller);

        return $this;
    }

    public function field(string $name, $type = 'text'): self
    {
        if (is_null($this->openSection)) {
            throw new NoSectionOpenException();
        }

        $this->openSection->addField(new Field($name, $this->viewTypeManager->get($type)));

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
}
