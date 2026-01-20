<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;

require __DIR__ . '/../vendor/autoload.php';

// ==================================================
// ENV
// ==================================================
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// ==================================================
// DOCTRINE CONFIG
// ==================================================
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/../src/Models'],
    isDevMode: $_ENV['APP_ENV'] !== 'production'
);

// ==================================================
// DATABASE
// ==================================================
$connectionParams = [
    'driver'   => 'pdo_mysql',
    'host'     => $_ENV['DB_HOST_PHINX'],
    'port'     => $_ENV['DB_PORT_PHINX'],
    'dbname'   => $_ENV['DB_DATABASE'],
    'user'     => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset'  => 'utf8mb4',
];

$connection = DriverManager::getConnection($connectionParams, $config);
$entityManager = new EntityManager($connection, $config);

// ==================================================
// DISPONIBILIZA O EM
// ==================================================
return $entityManager;
