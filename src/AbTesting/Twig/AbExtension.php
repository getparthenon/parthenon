<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Twig;

use Parthenon\AbTesting\Experiment\DeciderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AbExtension extends AbstractExtension
{
    private DeciderInterface $decider;

    public function __construct(DeciderInterface $decider)
    {
        $this->decider = $decider;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('ab_do_experiment', [$this->decider, 'doExperiment']),
        ];
    }
}
