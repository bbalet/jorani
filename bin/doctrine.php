#!/usr/bin/env php
<?php
//We define these constants in order to prevent the error "No direct script access allowed"
define('BASEPATH','.');
define('ENVIRONMENT','');

require dirname(__DIR__ ). '/bootstrap.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

ConsoleRunner::run(
    new SingleManagerProvider($entityManager)
);
