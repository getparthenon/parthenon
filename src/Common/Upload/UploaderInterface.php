<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploaderInterface
{
    public const PROVIDER_S3 = 's3';

    public const PROVIDER_LOCAL = 'local';

    public function uploadUploadedFile(UploadedFile $file): File;
}
