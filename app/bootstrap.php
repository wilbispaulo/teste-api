<?php

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;

require './vendor/autoload.php';

Dotenv::createImmutable('./app/')->load();

$app = AppFactory::create();

$basePath = dirname(__FILE__, 2);

// $twig = Twig::create($basePath . $_ENV['PATH_TO_VIEWS'], ['cache' => $basePath . $_ENV['PATH_TO_CACHE']]);
$twig = Twig::create($basePath . $_ENV['PATH_TO_VIEWS'], ['cache' => false]);
