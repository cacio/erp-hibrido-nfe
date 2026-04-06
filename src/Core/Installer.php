<?php

namespace App\Core;

use Doctrine\ORM\EntityManager;

class Installer
{
    public static function isInstalled(EntityManager $em): bool
    {
        return $em->getConnection()
                  ->fetchOne('SELECT COUNT(*) FROM sis_tenants') > 0;
    }
}
