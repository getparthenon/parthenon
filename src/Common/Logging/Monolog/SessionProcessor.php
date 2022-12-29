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

namespace Parthenon\Common\Logging\Monolog;

use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionProcessor implements ProcessorInterface
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function __invoke(array $record): array
    {
        if (!$this->session->has('parthenon_session_id')) {
            $sessionId = md5(microtime());
            $this->session->set('parthenon_session_id', $sessionId);
        } else {
            $sessionId = $this->session->get('parthenon_session_id');
        }

        $record['extra']['session_id'] = $sessionId;

        return $record;
    }
}
