<?php

namespace App\controllers;

use BingoCart\BingoCart;
use BingoPdf\BingoPdf;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BingoPdfController
{
    public function createOneSerieTwoCarts(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $args = $request->getParsedBody();

        if (!key_exists('error', $args)) {

            // PDF CREATE
            $bgFile = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . '.jpg';
            $jsonFile = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . '.json';
            $jsonData = json_decode(file_get_contents($jsonFile), true);

            $pdf = new BingoPdf;

            $pdf->setProtection(['copy', 'modify', 'extract', 'assemble'], $_ENV['PDF_PASS'], $_ENV['PDF_OWNER_PASS'], 0, null);

            $pdf->setFilePDF($args['serie'] . '.pdf');

            $page = 0;
            if ($args['from'] <= '0') {
                $args['from'] = 1;
            }
            if ($args['to'] <= '0' or $args['to'] > count($jsonData['carts'])) {
                $args['to'] = count($jsonData['carts']);
            }
            for ($n = (int)$args['from'] - 1; $n < (int)$args['to']; $n++) {
                $cart = $jsonData['carts'][$n];
                $cartNum = intval(substr($cart, 0, 3));
                if ($cartNum % 2 === 0) {
                    $offSetX = 85;
                } else {
                    $pdf->AddPage();
                    // $page++;
                    $page = ((int) ($cartNum / 2)) + 1;
                    $offSetX = 0;

                    // BACKGROUND IMAGE
                    $pdf->imageJpgPdf($bgFile, 0, 0, 210, 148.5);

                    $pdf->StartTransform();
                    $pdf->Rotate(90, 9, 135);
                    $pdf->textBoxPdf($jsonData['head']['owner'], 9, 135, 90, 5, ['style' => 'BI', 'size' => 10]);
                    $pdf->StopTransform();


                    $pdf->textBoxPdf($jsonData['head']['date'], 74, 7.5, 30, 5, []);
                    $pdf->textBoxPdf($jsonData['head']['title_serie'], 74, 12, 95, 6, ['style' => 'B', 'size' => 16]);
                    $pdf->textBoxPdf($jsonData['head']['jackpot'], 74, 19.4, 95, 11, []);

                    $bigCartNum = $args['serie'] . sprintf('%03d', $page);

                    // NUM BIG CART
                    $pdf->textBoxPdf($bigCartNum, 175, 8, 30, 5, ['font' => 'dejavusansmono', 'style' => 'B', 'size' => 11]);

                    // QRCODE
                    $pdf->qrCodePdf($bigCartNum, 180, 13, 15, 15, 'L');
                }

                // CART NUM
                $pdf->textBoxPdf(sprintf('%03d', $cartNum), 85 + $offSetX, 30, 10, 5, ['font' => 'dejavusansmono', 'style' => '', 'size' => 14]);

                // BALLS
                $balls = BingoCart::splitWord($cart);
                $i = 1;
                $ballOffSetX = 0;
                $ballOffSetY = 0;
                $argsTxt = [
                    'font' => 'times',
                    'style' => '',
                    'size' => 30,
                    'align' => 'C',
                    'valign' => 'M'
                ];
                foreach ($balls as $ball) {
                    if ($i !== 13) {
                        $pdf->textBoxPdf($ball, 14 + 16 * (($i - 1) % 5) + $offSetX, 55.4 + 16 * ((int) (($i - 1) / 5)), 16, 16, $argsTxt);
                    }
                    $i++;
                }

                // HASH CART
                $pdf->textBoxPdf(BingoCart::getHashCart($cart), 18 + $offSetX, 135, 75, 5, ['font' => 'dejavusansmono', 'style' => '', 'size' => 10]);
            }

            $pdf->lastPage();
            $pdfBinary = $pdf->render();
            $response->getBody()->write($pdfBinary);
            return $response
                ->withHeader('Content-Type', 'application/pdf')
                ->withStatus(200);
        }

        $contents = $args;
        $response->getBody()->write(
            json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
