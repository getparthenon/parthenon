<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Logging\Monolog;

use Monolog\Processor\ProcessorInterface;

final class ExceptionProcessor implements ProcessorInterface
{
    public function __invoke(array $record)
    {
        $output = [];

        foreach ($record as $key => $value) {
            if (is_array($value)) {
                $output[$key] = $this->__invoke($value);
            } elseif ($value instanceof \Throwable) {
                $output[$key] = [
                    'message' => $value->getMessage(),
                    'file' => $value->getFile(),
                    'line' => $value->getLine(),
                    'code' => $value->getCode(),
                ];
            } else {
                $output[$key] = $value;
            }
        }

        return $output;
    }
}
