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

namespace Parthenon\Common\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploaderInterface
{
    public const PROVIDER_S3 = 's3';

    public const PROVIDER_LOCAL = 'local';

    public function uploadString(string $filename, string $contents): File;

    public function uploadUploadedFile(UploadedFile $file): File;

    public function deleteFile(File $file): void;

    /**
     * @return resource
     */
    public function readFile(File $file);
}
