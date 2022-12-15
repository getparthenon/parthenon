<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
