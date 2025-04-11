<?php

namespace App\middlewares;

use App\library\HttpInvalidSignException;
use BingoCart\BingoCart;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonFileSignatureValidatorMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uploadedFiles = $request->getUploadedFiles();

        $file['json'] = [];
        foreach ($uploadedFiles['jsonFile'] as $uploadedFile) {
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                if (!self::verifySign($uploadedFile)) {
                    $file['json'][$uploadedFile->getClientFilename()] = 'invalid_sign';
                    continue;
                }
                $filename = self::moveUploadedFile(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'], $uploadedFile);
                $file['json'][$filename] = 'ok';
            }
        }

        return $handler->handle($request->withParsedBody($file));
    }

    private static function verifySign(UploadedFileInterface $uploadedFile): bool
    {
        $json = $uploadedFile->getStream()->getContents();
        $cert = dirname(__FILE__, 3) . $_ENV['PATH_TO_CERT'];
        return BingoCart::validateSignJson($json, $cert);
    }

    private static function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile)
    {
        $json = $uploadedFile->getStream()->getContents();
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }
}
