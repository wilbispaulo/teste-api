<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

$notFoundErrorHandler = function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
    $response = new Response();
    $response->getBody()->write(json_encode(['error' => '404_not_found']));
    return $response
        ->withStatus(404)
        ->withHeader('Content-Type', 'application/json');
};

$methodNotAllowedErrorHandler = function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
    $response = new Response();
    $response->getBody()->write(json_encode(['error' => '405_method_not_allowed']));
    return $response
        ->withStatus(405)
        ->withHeader('Content-Type', 'application/json');
};

$unauthorizedErrorHandler = function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
    $response = new Response();
    $response->getBody()->write(json_encode(['error' => '401_unauthorized']));
    return $response
        ->withStatus(401)
        ->withHeader('Content-Type', 'application/json');
};

$badRequestErrorHandler = function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
    $response = new Response();
    $response->getBody()->write(json_encode(['error' => '400_bad_request']));
    return $response
        ->withStatus(400)
        ->withHeader('Content-Type', 'application/json');
};
