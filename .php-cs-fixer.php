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

return (new PhpCsFixer\Config())
            ->setRiskyAllowed(true)
            ->setRules([
                '@PSR2' => true,
                '@Symfony' => true,
                'header_comment' => ['header' => 'Copyright Iain Cambridge 2020-2025.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
the Free Software Foundation, either version 2.1 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.'],
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
