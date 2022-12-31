<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Export\Notification;

use Parthenon\Common\FieldAccesorTrait;
use Parthenon\Export\BackgroundEmailExportRequest;
use Parthenon\Notification\Email;

class ExportEmailFactory implements ExportEmailFactoryInterface
{
    use FieldAccesorTrait;

    public function buildEmail(BackgroundEmailExportRequest $exportRequest): Email
    {
        $user = $exportRequest->getUser();

        $emailAddress = $this->getFieldData($user, 'email');
        $email = new Email();
        $email->setToAddress($emailAddress);
        $email->setSubject('Export');
        $email->setContent('See attached');

        return $email;
    }
}
