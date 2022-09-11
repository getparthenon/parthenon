<?php

return PhpCsFixer\Config::create()
            ->setRiskyAllowed(true)
            ->setRules([
                '@PSR2' => true,
                '@Symfony' => true,
                'header_comment' => ['header' => "Copyright Humbly Arrogant Ltd 2020-2021, all rights reserved."],
                'visibility_required' => ['property', 'method', 'const'],
                'list_syntax' => ['syntax' => 'short'],
                'array_syntax' => ['syntax' => 'short'],
                'declare_strict_types' => true,
            ])
            ->setFinder(
        PhpCsFixer\Finder::create()->in(__DIR__)
        )
        ;
