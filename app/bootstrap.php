<?php

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require './vendor/autoload.php';

Dotenv::createImmutable('./app/')->load();

$app = AppFactory::create();
