<?php

namespace App\controllers;

use BingoCart\BingoCart;
use BingoPdf\BingoPdf;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BingoPdfController
{
    private $cartXoffSet = [0, 98, 0, 98];
    private $cartYoffSet = [0, 0, 148.5, 148.5];
    private $barCodeStyle = [
        'position' => '',
        'align' => 'C',
        'stretch' => false,
        'fitwidth' => true,
        'cellfitalign' => '',
        'border' => false,
        'hpadding' => 0,
        'vpadding' => 0,
        'fgcolor' => [0, 0, 0],
        'bgcolor' => false,
        'text' => true,
        'font' => 'pdfacourierb',
        'fontsize' => 12,
        'stretchtext' => 3
    ];

    public function createOneSerieTwoCartsTwoSideA4(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $args = $request->getParsedBody();

        if (!key_exists('error', $args)) {
            //var_dump($args);
            // PDF CREATE
            $bgFileFr = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . 'fr.jpg';
            $bgFileBk = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . 'bk.jpg';
            $jockerPng = dirname(__FILE__, 3) . '\\app\\assets\\img' . DIRECTORY_SEPARATOR . $_ENV['JOCKER_JPG'];
            $jsonFile = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . '.json';
            $jsonData = json_decode(file_get_contents($jsonFile), true);

            // var_dump($jsonFile);
            // var_dump($bgFileBk);
            // var_dump($bgFileFr);
            // var_dump($jockerPng);
            // var_dump($jsonData);
            // die;

            $pdf = new BingoPdf('A4', 'P');

            $pdf->setProtection(['copy', 'modify', 'extract', 'assemble'], $_ENV['PDF_PASS'], $_ENV['PDF_OWNER_PASS'], 0, null);

            $pdf->setFilePDF($args['serie'] . '.pdf');

            if ($args['from'] <= '0') {
                $args['from'] = 1;
            }
            if ($args['to'] <= '0' or $args['to'] > count($jsonData['carts'])) {
                $args['to'] = count($jsonData['carts']);
            }

            $page = 0;

            for ($n = (int)$args['from'] - 1; $n < (int)$args['to']; $n++) {
                $cart = $jsonData['carts'][$n];
                $cartNum = intval(substr($cart, 0, 3));
                $cartItem = $n % 4;

                if ($cartItem === 0) {
                    $page++;
                    // NOVA PÁGINA (FRENTE)
                    $pdf->AddPage();

                    // BACKGROUND IMAGE (FRENTE)
                    $pdf->imageJpgPdf($bgFileFr, 0, 0, 210, 297);

                    // BARCODE CART 1
                    $bigCartNum = $args['serie'] . sprintf('%03d', $page * 2 - 1);
                    $pdf->qrCodePdf($bigCartNum, 178, 115 + $this->cartYoffSet[0], 20, 20, 'Q');
                    $pdf->textPdf(substr($bigCartNum, 6), 178, 135 + $this->cartYoffSet[0], 20, 10, 'C', '#751414', '#ffffff', ['font' => 'dejavusansmono', 'style' => 'B', 'size' => 18]);

                    // BARCODE CART 2
                    $bigCartNum = $args['serie'] . sprintf('%03d', $page * 2);
                    $pdf->qrCodePdf($bigCartNum, 178, 115 + $this->cartYoffSet[2], 20, 20, 'Q');
                    $pdf->textPdf(substr($bigCartNum, 6), 178, 135 + $this->cartYoffSet[2], 20, 10, 'C', '#751414', '#ffffff', ['font' => 'dejavusansmono', 'style' => 'B', 'size' => 18]);

                    // NOVA PÁGINA (VERSO)
                    $pdf->AddPage();
                    // BACKGROUND IMAGE (VERSO)
                    $pdf->imageJpgPdf($bgFileBk, 0, 0, 210, 297);

                    // PROPRIETÁRIO CART 1 (TEXTO VERTICAL)
                    $pdf->StartTransform();
                    // $pdf->Rotate(90, 0, 0);
                    $pdf->Rotate(90, 11, 134);
                    $pdf->textBoxPdf($jsonData['head']['owner'], 11, 134, 90, 13, ['style' => 'BI', 'size' => 10]);
                    $pdf->StopTransform();

                    // CABEÇALHO CART 1 (TEXTO HORIZONTAL)
                    $pdf->textBoxPdf($jsonData['head']['date'], 27, 7.5 + $this->cartYoffSet[0], 30, 11, []);
                    $pdf->textBoxPdf($jsonData['head']['title_serie'], 32, 12 + $this->cartYoffSet[0], 100, 11, ['style' => 'B', 'size' => 16]);
                    $pdf->textBoxPdf($jsonData['head']['jackpot'], 32, 19.4 + $this->cartYoffSet[0], 130, 11, []);

                    // PROPRIETÁRIO CART 2 (TEXTO VERTICAL)
                    $pdf->StartTransform();
                    $pdf->Rotate(90, 11, 134 + $this->cartYoffSet[2]);
                    $pdf->textBoxPdf($jsonData['head']['owner'], 11, 134 + $this->cartYoffSet[2], 90, 13, ['style' => 'BI', 'size' => 10]);
                    $pdf->StopTransform();

                    // CABEÇALHO CART 2 (TEXTO HORIZONTAL)
                    $pdf->textBoxPdf($jsonData['head']['date'], 27, 7.5 + $this->cartYoffSet[2], 30, 11, []);
                    $pdf->textBoxPdf($jsonData['head']['title_serie'], 32, 12 + $this->cartYoffSet[2], 100, 11, ['style' => 'B', 'size' => 16]);
                    $pdf->textBoxPdf($jsonData['head']['jackpot'], 32, 19.4 + $this->cartYoffSet[2], 130, 11, []);

                    // CÓDIGO DE BARRAS CARTELA VERSO
                    $bigCartNum = $args['serie'] . sprintf('%03d', $page * 2 - 1);
                    // QRCODE CART 1
                    $pdf->qrCodePdf($bigCartNum, 175, 9 + $this->cartYoffSet[0], 15, 15, 'Q');
                    $bigCartNum = $args['serie'] . sprintf('%03d', $page * 2);
                    // QRCODE CART 2
                    $pdf->qrCodePdf($bigCartNum, 175, 9 + $this->cartYoffSet[2], 15, 15, 'Q');
                    $bigCartNum = $args['serie'] . sprintf('%03d', $page * 2);
                }

                // CART NUM
                $pdf->textBoxPdf(sprintf('%03d', $cartNum), 82 + $this->cartXoffSet[$cartItem], 30 + $this->cartYoffSet[$cartItem], 10, 5, ['font' => 'dejavusansmono', 'style' => '', 'size' => 14]);

                // BALLS
                $balls = BingoCart::splitWord($cart);
                $i = 1;
                $argsTxt = [
                    'font' => 'times',
                    'style' => '',
                    'size' => 30,
                    'align' => 'C',
                    'valign' => 'M'
                ];
                foreach ($balls as $ball) {
                    if ($i !== 13) {
                        $pdf->textBoxPdf($ball, 16 + 16 * ((int)(($i - 1) / 5)) + $this->cartXoffSet[$cartItem], (55 + 16 * ((int) (($i - 1) % 5))) + $this->cartYoffSet[$cartItem], 16, 16, $argsTxt);
                    } else {
                        // JOCKER
                        $pdf->imagePngPdf($jockerPng, 17 + 16 * ((int)(($i - 1) / 5)) + $this->cartXoffSet[$cartItem], (55 + 16 * (($i - 1) % 5)) + $this->cartYoffSet[$cartItem], 14, 14);
                    }
                    $i++;
                }

                // HASH CART
                $pdf->textBoxPdf(BingoCart::getHashCart($cart), 18 + $this->cartXoffSet[$cartItem], 135 + $this->cartYoffSet[$cartItem], 75, 5, ['font' => 'dejavusansmono', 'style' => '', 'size' => 10]);
            }

            $pdf->lastPage();
            $pdfBinary = $pdf->render();
            $response->getBody()->write($pdfBinary);
            // var_dump('PDF gerado com sucesso!');
            // die;
            return $response
                ->withHeader('Content-Type', 'application/pdf')
                ->withStatus(200);
        }
        $contents = $args;
        var_dump('PDF falhou!');
        var_dump($args);
        die;
        $response->getBody()->write(
            json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function createOneSerieTwoCartsOneSideA5(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $args = $request->getParsedBody();

        if (!key_exists('error', $args)) {

            // PDF CREATE
            $bgFile = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . '.jpg';
            $jsonFile = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . '.json';
            $jsonData = json_decode(file_get_contents($jsonFile), true);

            $pdf = new BingoPdf();

            $pdf->setProtection(['copy', 'modify', 'extract', 'assemble'], $_ENV['PDF_PASS'], $_ENV['PDF_OWNER_PASS'], 0, null);

            $pdf->setFilePDF($args['serie'] . '.pdf');

            if ($args['from'] <= '0') {
                $args['from'] = 1;
            }
            if ($args['to'] <= '0' or $args['to'] > count($jsonData['carts'])) {
                $args['to'] = count($jsonData['carts']);
            }

            $page = 0;

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
