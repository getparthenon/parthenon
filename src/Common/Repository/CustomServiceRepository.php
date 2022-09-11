<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CustomServiceRepository extends ServiceEntityRepository
{
    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager()
    {
        return parent::getEntityManager();
    }
}
