<?php

namespace App\controllers;

use BingoCart\BingoCart;
use BingoCart\library\HashSSl;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BingoController
{
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $seriesParams = $request->getParsedBody();

        $sslHash = new HashSSl(dirname(__FILE__, 3) . $_ENV['PATH_TO_BINGO_CERT'], $_ENV['BINGO_CERT_SECRET']);
        $bingo = new BingoCart(
            $sslHash,
            $seriesParams['date'],
            $seriesParams['serie'],
            $seriesParams['owner'],
            $seriesParams['title_serie'],
            $seriesParams['title_event'],
            $seriesParams['jackpot'],
        );

        $serieCarts = $bingo->makeSeries($seriesParams['qty_cart']);
        $json = json_encode($serieCarts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $seriesSign = BingoCart::signJson($json, $sslHash->getPrivatePEM());

        $response->getBody()->write(
            $seriesSign
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
