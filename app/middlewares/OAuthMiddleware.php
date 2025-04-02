<?php

namespace App\middlewares;

use AuthCliJwt\OAuthCli;
use Slim\Exception\HttpUnauthorizedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class OAuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        $pathToCert = dirname(__DIR__, 2) . $_ENV['PATH_TO_CERT'];

        $auth = new OAuthCli($pathToCert, $_ENV['ISSUER'], $_ENV['AUDIENCE']);

        $endpoint = trim($request->getRequestTarget(), '/') . '/' . strtolower($request->getMethod());
        $result = $auth->checkOAuth($endpoint);

        if (array_key_exists('error', $result)) {
            return throw new HttpUnauthorizedException($request);
        }

        return $response;
    }
}
