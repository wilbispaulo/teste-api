<?php

namespace App\middlewares;

use AuthCliJwt\OAuthCli;
use Slim\Exception\HttpUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OAuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $pathToCert = dirname(__DIR__, 2) . $_ENV['PATH_TO_CERT'];

        $auth = new OAuthCli($pathToCert, $_ENV['ISSUER'], $_ENV['AUDIENCE']);

        $endpoint = trim($request->getRequestTarget(), '/') . '/' . strtolower($request->getMethod());
        var_dump($endpoint);
        $result = $auth->checkOAuth($endpoint);


        if (array_key_exists('error', $result)) {
            return throw new HttpUnauthorizedException($request);
        }
        return $response;
    }
}
