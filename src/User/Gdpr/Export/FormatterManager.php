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

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Exception\Gdpr\NoFormatterFoundException;
use Symfony\Component\HttpFoundation\Response;

final class FormatterManager implements FormatterManagerInterface
{
    /**
     * @var FormatterInterface[]
     */
    private array $formatters = [];
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function add(FormatterInterface $formatter): void
    {
        $this->formatters[] = $formatter;
    }

    public function format(UserInterface $user, array $data): Response
    {
        foreach ($this->formatters as $formatter) {
            if ($formatter->getName() === $this->type) {
                $filename = $formatter->getFilename($user);
                $data = $formatter->format($data);

                return new Response($data, Response::HTTP_OK, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => sprintf('attachment; filename=%s', $filename),
                    'Content-Description' => 'File Transfer',
                ]);
            }
        }

        throw new NoFormatterFoundException(sprintf('No formatter found for type set %s', $this->type));
    }
}
