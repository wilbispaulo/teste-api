<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class AppController
{
    public function bingoUpload(ServerRequestInterface $request, ResponseInterface $response)
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'fileupload.html.twig');
    }
}
