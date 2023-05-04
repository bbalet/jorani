<?php
/**
 * This bootstrap file will help us to use Doctrine meanwhile
 * we are migrating to Symfony framework
 * @copyright  Copyright (c) 2014-2023 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      1.1.0
 */
require_once __DIR__."/vendor/autoload.php";

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

// Create a simple "default" Doctrine ORM configuration for Attributes
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__."/src"),
    isDevMode: true,
);

// get the database connection from CI 3 configuration
$env = is_null(getenv('CI_ENV'))?'':getenv('CI_ENV');
$pathConfigFile = realpath(join(DIRECTORY_SEPARATOR, array(__DIR__, 'application', 'config', $env, 'database.php')));
include($pathConfigFile);

$dsnParser = new DsnParser(['mysql' => 'pdo_mysql', 'postgres' => 'pdo_pgsql']);
$connectionParams = $dsnParser->parse($db[$active_group]['dsn']);
$connectionParams['user'] ??= $db[$active_group]['username'];
$connectionParams['password'] ??= $db[$active_group]['password'];

$connection = DriverManager::getConnection($connectionParams);

// obtaining the entity manager
$entityManager = new EntityManager($connection, $config);
$GLOBALS['entityManager'] = $entityManager;
