<?php

use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

require './app/bootstrap.php';
require './app/middlewares/errorHandlers.php';
require './app/middlewares/corsMiddleware.php';

require './app/routes/web.php';

$app->add($corsMiddleware);

$app->addErrorMiddleware(true, true, true)
    ->setErrorHandler(HttpNotFoundException::class, $notFoundErrorHandler)
    ->setErrorHandler(HttpMethodNotAllowedException::class, $methodNotAllowedErrorHandler)
    ->setErrorHandler(HttpBadRequestException::class, $badRequestErrorHandler)
    ->setErrorHandler(HttpUnauthorizedException::class, $unauthorizedErrorHandler);

$app->run();
