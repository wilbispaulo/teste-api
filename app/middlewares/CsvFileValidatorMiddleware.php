<?php

namespace App\middlewares;

use App\library\Helpers;
use App\library\HttpInvalidSignException;
use BingoCart\BingoCart;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsvFileValidatorMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uploadedFiles = $request->getUploadedFiles();

        $file['csv'] = [];
        foreach ($uploadedFiles['csvfile'] as $uploadedFile) {
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                if (!$data = self::verifyCsv($uploadedFile)) {
                    $result['csv'][$uploadedFile->getClientFilename()] = 'invalid_csv';
                    continue;
                } else {
                    $result = $data;
                }
                $filename = Helpers::moveUploadedFile(dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'], $uploadedFile);
                //$file['csv'][$filename] = 'ok';

            }
        }

        return $handler->handle($request->withParsedBody($result));
    }

    private static function verifyCsv(UploadedFileInterface $uploadedFile): bool | array
    {
        $ok = true;
        $csv = explode("\r\n", $uploadedFile->getStream()->getContents());
        $ok &= (substr($csv[0], 0, 25) === "SERIE...................:");
        $ok &= (substr($csv[1], 0, 25) === "DATA....................:");
        $ok &= (substr($csv[2], 0, 25) === "TITULO SERIE............:");
        $ok &= (substr($csv[3], 0, 25) === "TITULO EVENTO...........:");
        $ok &= (substr($csv[4], 0, 25) === "PREMIO..................:");
        $ok &= (substr($csv[5], 0, 25) === "PROPRIETARIO............:");
        $ok &= (substr($csv[6], 0, 25) === "QUANTIDADE DE CARTELAS..:");
        $ok &= (substr($csv[7], 0, 25) === "MAXIMA REPETICAO........:");
        $ok &= ($csv[8] === "====================================================");

        return ($ok ? $csv : false);
    }
}
