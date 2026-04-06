<?php

namespace App\Core;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Dotenv\Dotenv;
use Doctrine\DBAL\Logging\DebugStack;

class EntityManagerFactory
{
    public static function create(): EntityManager
    {
        // 🔹 Carrega .env da RAIZ do projeto
        $rootPath = dirname(__DIR__, 2);

        if (file_exists($rootPath . '/.env')) {
            $dotenv = Dotenv::createImmutable($rootPath);
            $dotenv->load();
        }

        $isDevMode = ($_ENV['APP_ENV'] ?? 'dev') !== 'prod';

        $config = ORMSetup::createAttributeMetadataConfiguration(
            [dirname(__DIR__) . '/Models'],
            $isDevMode
        );

        $connection = [
            'driver'   => 'pdo_mysql',
            'host'     => $_ENV['DB_HOST'],
            'port'     => $_ENV['DB_PORT'],
            'dbname'   => $_ENV['DB_DATABASE'],
            'user'     => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'charset'  => 'utf8mb4',
        ];

        $debugStack = new DebugStack();

        $entityManager = EntityManager::create($connection, $config);

        $GLOBALS['doctrine_sql_logger'] = $debugStack;

        return $entityManager;
    }
}
