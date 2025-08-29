<?php

namespace App\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class BingoFiles1SideValidatorMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $args = RouteContext::fromRequest($request)->getRoute()->getArguments();

        if (!file_exists(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . '/' . $args['serie'] . '.json')) {
            return $handler->handle($request->withParsedBody([
                'error' => 'json_file_missing',
                'msg' => 'Json file is missing.'
            ]));
        };

        if (!file_exists(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . '/' . $args['serie'] . '.jpg')) {
            return $handler->handle($request->withParsedBody([
                'error' => 'image_file_missing',
                'msg' => 'Image file is missing.'
            ]));
        };

        return $handler->handle($request->withParsedBody($args));
    }
}
