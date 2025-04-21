<?php

namespace App\library;

use Psr\Http\Message\UploadedFileInterface;

class Helpers
{
    public static function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile)
    {
        // var_dump($uploadedFile->getClientFilename());
        // die();
        // $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = pathinfo($uploadedFile->getClientFilename(), PATHINFO_BASENAME);
        // $basename = bin2hex(random_bytes(8));
        // $filename = sprintf('%s.%0.8s', $basename, $extension);
        $filename = $basename;
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }
}
