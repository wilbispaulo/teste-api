<?php

namespace App\controllers;

use App\library\Helpers;
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

    public function uploadBack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $file = [];
        $uploadedFile = $request->getUploadedFiles()['jpegFile'][0];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = Helpers::moveUploadedFile(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'], $uploadedFile);
            $file[$filename] = 'ok';
        }


        $contents = $file;
        $response->getBody()->write(
            json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
