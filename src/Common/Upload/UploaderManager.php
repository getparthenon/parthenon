<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common\Upload;

use Parthenon\Common\Exception\Upload\NoUploaderFoundException;
use Parthenon\Common\Upload\Factory\FactoryInterface;

final class UploaderManager implements UploadManagerInterface
{
    public function __construct(private array $configs, private FactoryInterface $factory)
    {
    }

    public function getUploader(string $name = 'default'): UploaderInterface
    {
        if ('default' === $name && 1 == count($this->configs)) {
            $config = current($this->configs);
        } elseif (!isset($this->configs[$name])) {
            throw new NoUploaderFoundException(sprintf('There is no uploader by the name "%s".', $name));
        } else {
            $config = $this->configs[$name];
        }

        return $this->factory->build($config);
    }
}
