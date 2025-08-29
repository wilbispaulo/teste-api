<?php

namespace App\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class BingoFiles2SideValidatorMiddleware implements MiddlewareInterface
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

        if (!file_exists(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . '/' . $args['serie'] . 'fr.jpg')) {
            return $handler->handle($request->withParsedBody([
                'error' => 'image_file_missing',
                'msg' => 'Front image file is missing (AAMMDD##fr.jpg).'
            ]));
        };

        if (!file_exists(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . '/' . $args['serie'] . 'bk.jpg')) {
            return $handler->handle($request->withParsedBody([
                'error' => 'image_file_missing',
                'msg' => 'Back image file is missing (AAMMDD##bk.jpg).'
            ]));
        };

        return $handler->handle($request->withParsedBody($args));
    }
}
