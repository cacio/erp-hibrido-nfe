<?php

namespace Core;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class Doctrine
{
    public static function getEntityManager(): EntityManager
    {
        $paths = [__DIR__ . '/../app/Models'];
        $isDevMode = true;

        $config = Setup::createAnnotationMetadataConfiguration(
            $paths,
            $isDevMode,
            null,
            null,
            false
        );

        $dbParams = require __DIR__ . '/../config/database.php';

        return EntityManager::create($dbParams, $config);
    }
}
