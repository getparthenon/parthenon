<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

namespace Parthenon\Common\Logging\Monolog;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

final class ExceptionProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record)
    {
        $output = [];

        $output = $this->getArr($record, $output);

        return $output;
    }

    /**
     * @param LogRecord $record
     *
     * @return array
     */
    public function getArr(mixed $record, array $output)
    {
        foreach ($record as $key => $value) {
            if (is_array($value)) {
                $output[$key] = $this->getArr($value, $output);
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
