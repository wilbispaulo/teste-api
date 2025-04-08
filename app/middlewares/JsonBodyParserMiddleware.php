<?php

namespace App\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonBodyParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $credentials = $request->getParsedBody();

        // CONTENT-TYPE VALIDATION
        if (!strstr($request->getHeaderLine('Content-Type'), 'application/json')) {
            throw new HttpBadRequestException($request);
        }

        // JSON VALIDATION
        $contents = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpBadRequestException($request);
        }

        if (isset($credentials)) {
            $contents = array_merge($credentials, $contents);
        }

        return $handler->handle($request->withParsedBody($contents));
    }
}
