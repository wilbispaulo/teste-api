<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class TesteController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = [
            'controller' => 'Teste Controller',
            'method' => 'index'
        ];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function teste(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!preg_match('/^[0-9]+$/', $args['id'])) {
            return throw new HttpBadRequestException($request);
        }
        $data = [
            'controller' => 'Teste Controller',
            'method' => 'teste',
            'id' => $args['id'],
        ];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
