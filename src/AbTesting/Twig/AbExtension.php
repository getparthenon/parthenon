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
