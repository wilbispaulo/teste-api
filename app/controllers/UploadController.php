<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadController
{
    public function uploadJson(ServerRequestInterface $request, ResponseInterface $response)
    {
        $uploadedFiles = $request->getUploadedFiles();

        foreach ($uploadedFiles['jsonFile'] as $uploadedFile) {
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'], $uploadedFile);
            }
        }
        var_dump($uploadedFiles);
        die();
    }

    private function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }
}
