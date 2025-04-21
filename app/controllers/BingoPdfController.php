<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BingoPdfController
{
    public function createOneSerieTwoCarts(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $cartsParams = $request->getParsedBody();

        var_dump($cartsParams);
        die();

        return $response;
    }
}
