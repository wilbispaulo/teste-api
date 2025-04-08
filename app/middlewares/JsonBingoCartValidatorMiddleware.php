<?php

namespace App\middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonBingoCartValidatorMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contents = $request->getParsedBody();

        $ok = true;

        // key validation
        $requestKeys = array_keys($contents);
        if (count($requestKeys) !== 7) {
            throw new HttpBadRequestException($request);
        }

        $tplKey = [
            'date',
            'serie',
            'owner',
            'title_serie',
            'title_event',
            'jackpot',
            'qty_cart'
        ];
        if (count(array_diff($tplKey, $requestKeys)) > 0) {
            throw new HttpBadRequestException($request);
        }

        return $handler->handle($request->withParsedBody($contents));
    }
}
