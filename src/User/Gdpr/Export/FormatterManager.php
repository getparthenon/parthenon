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
