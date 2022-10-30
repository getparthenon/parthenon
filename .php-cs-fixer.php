<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

return (new PhpCsFixer\Config())
            ->setRiskyAllowed(true)
            ->setRules([
                '@PSR2' => true,
                '@Symfony' => true,
                'header_comment' => ['header' => 'Copyright Iain Cambridge 2020-2022.
                
Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.

Change Date: TBD ( 3 years after 2.1.0 release )

On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.'],
                'list_syntax' => ['syntax' => 'short'],
                'array_syntax' => ['syntax' => 'short'],
                'declare_strict_types' => true,
                'ordered_class_elements' => true,
                'no_multiple_statements_per_line' => true,
                'constant_case' => true,
                'no_useless_nullsafe_operator' => true,
            ])
            ->setFinder(
                PhpCsFixer\Finder::create()->in(__DIR__)
            )
;
