<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UploadController
{
    public function uploadJson(ServerRequestInterface $request, ResponseInterface $response)
    {
        $contents = $request->getParsedBody();
        $response->getBody()->write(
            json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
