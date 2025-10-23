<?php

namespace App\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class BingoFiles2Side3JsonValidatorMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $args = RouteContext::fromRequest($request)->getRoute()->getArguments();

        $serieParts = explode('-', $args['serie']);

        if (count($serieParts) != 3) {
            return $handler->handle($request->withParsedBody([
                'error' => 'invalid_serie_parameter',
                'msg' => 'The serie parameter must contain three serie IDs separated by hyphens (e.g., AAMMDD#1-AAMMDD#2-AAMMDD#3).'
            ]));
        }

        $args['serie'] = substr($serieParts[0], 0, 6);
        $args['serie1'] = $serieParts[0];
        $args['serie2'] = $serieParts[1];
        $args['serie3'] = $serieParts[2];

        if (!file_exists(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . '/' . $args['serie1'] . '.json')) {
            return $handler->handle($request->withParsedBody([
                'error' => 'json_file_missing',
                'msg' => 'Json file is missing.'
            ]));
        };

        if (!file_exists(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . '/' . $args['serie2'] . '.json')) {
            return $handler->handle($request->withParsedBody([
                'error' => 'json_file_missing',
                'msg' => 'Json file is missing.'
            ]));
        };

        if (!file_exists(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . '/' . $args['serie3'] . '.json')) {
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
