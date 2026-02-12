<?php

namespace App\controllers;

use BingoCart\BingoCart;
use BingoPdf\BingoPdf;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BingoPdfController
{
    private $cartXoffSet = [0, 100];
    private $cartYoffSet = [0, 0];
    private $posIni = [
        'barcode_serie_x' => 16,
        'barcode_serie_y' => 65,
        'title_serie_x' => 28,
        'title_serie_y' => 11,
        'jackpot_x' => 28,
        'jackpot_y' => 20,
        'ball_x' => 14.5,
        'ball_y' => 50,
        'ball_width' => 16,
        'ball_height' => 16,
        'hash_x' => 17,
        'hash_y' => 130
    ];
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
        'font' => 'dejavusansmono',
        'fontsize' => 12,
        'stretchtext' => 3
    ];

    public function createTwoCartsTwoSideA5(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $args = $request->getParsedBody();

        $this->cartXoffSet = [0, 100];
        $this->cartYoffSet = [0, 0];
        $this->posIni = [
            'barcode_serie_x' => 65,
            'barcode_serie_y' => 131,
            'title_serie_x' => 28,
            'title_serie_y' => 5,
            'jackpot_x' => 28,
            'jackpot_y' => 14,
            'ball_x' => 14.5,
            'ball_y' => 44,
            'ball_width' => 16,
            'ball_height' => 16,
            'hash_x' => 17,
            'hash_y' => 124
        ];

        if (!key_exists('error', $args)) {

            $twoSeries = ($args['serie_count'] == 2 ? true : false);

            // PDF CREATE

            $bgFileFr = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . 'fr.jpg';
            $bgFileBk = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . 'bk.jpg';
            $jockerPng = dirname(__FILE__, 3) . '\\app\\assets\\img' . DIRECTORY_SEPARATOR . $_ENV['JOCKER_JPG'];
            $jsonFile1 = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie1'] . '.json';
            $jsonData1 = json_decode(file_get_contents($jsonFile1), true);
            if ($twoSeries) {
                $jsonFile2 = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie2'] . '.json';
                $jsonData2 = json_decode(file_get_contents($jsonFile2), true);
            }

            $pdf = new BingoPdf('A5', 'L');

            $pdf->setProtection(['copy', 'modify', 'extract', 'assemble'], $_ENV['PDF_PASS'], $_ENV['PDF_OWNER_PASS'], 0, null);

            $pdf->setFilePDF($args['serie'] . '.pdf');

            if ($args['from'] <= '0') {
                $args['from'] = 1;
            }
            if ($args['to'] <= '0' or $args['to'] > count($jsonData1['carts'])) {
                $args['to'] = count($jsonData1['carts']);
            }

            // GERA O PDF (A5 - 2 LADOS - 2 SERIES - 2 CARTELAS )
            // --------------------------------------------------

            $page = 0;

            for ($n = (int)$args['from'] - 1; $n < (int)$args['to']; $n++) {
                // CARTELAS
                $page++;
                // NOVA PÁGINA (FRENTE)
                $pdf->AddPage();

                // BACKGROUND IMAGE (FRENTE)
                $pdf->imageJpgPdf($bgFileFr, 0, 0, 210, 148);

                // BARCODE CART 1
                $bigCartNum = $args['serie'] . sprintf('%03d', $n + 1);
                $pdf->qrCodePdf($bigCartNum, 177.5, 113 + $this->cartYoffSet[0], 20, 20, 'Q');
                $pdf->textPdf($bigCartNum, 170, 135 + $this->cartYoffSet[0], 35, 10, 'C', '#751414', '#ffffff', ['font' => 'dejavusansmono', 'style' => 'B', 'size' => 16]);

                // NOVA PÁGINA (VERSO)
                $pdf->AddPage();

                // BACKGROUND IMAGE (FRENTE)
                $pdf->imageJpgPdf($bgFileBk, 0, 0, 210, 148);

                if ($twoSeries) {
                    // BARCODE SERIE 1
                    $pdf->barCodePdf($args['serie1'] . sprintf('%03d', $n + 1), $this->posIni['barcode_serie_x'] + $this->cartXoffSet[0], $this->posIni['barcode_serie_y'], 30, 10, 'I25+', 0.4, $this->barCodeStyle);

                    // BARCODE SERIE 2
                    $pdf->barCodePdf($args['serie2'] . sprintf('%03d', $n + 1), $this->posIni['barcode_serie_x'] + $this->cartXoffSet[1], $this->posIni['barcode_serie_y'], 30, 10, 'I25+', 0.4, $this->barCodeStyle);

                    // CABEÇALHO SERIE 1 (TEXTO HORIZONTAL)
                    $pdf->textBoxPdf($jsonData1['head']['title_serie'], $this->posIni['title_serie_x'] + $this->cartXoffSet[0], $this->posIni['title_serie_y'] + $this->cartYoffSet[0], 65, 11, ['style' => 'B', 'size' => 16]);
                    $pdf->textBoxPdf($jsonData1['head']['jackpot'], $this->posIni['jackpot_x'] + $this->cartXoffSet[0], $this->posIni['jackpot_y'] + $this->cartYoffSet[0], 65, 11, []);

                    // CABEÇALHO SERIE 2 (TEXTO HORIZONTAL)
                    $pdf->textBoxPdf($jsonData2['head']['title_serie'], $this->posIni['title_serie_x'] + $this->cartXoffSet[1], $this->posIni['title_serie_y'] + $this->cartYoffSet[1], 65, 11, ['style' => 'B', 'size' => 16]);
                    $pdf->textBoxPdf($jsonData2['head']['jackpot'], $this->posIni['jackpot_x'] + $this->cartXoffSet[1], $this->posIni['jackpot_y'] + $this->cartYoffSet[1], 65, 11, []);
                }

                for ($cartPos = 1; $cartPos <= 2; $cartPos++) {
                    switch ($cartPos) {
                        case 1:
                            $jsonData = $jsonData1;
                            break;
                        case 2:
                            if ($twoSeries) {
                                $jsonData = $jsonData2;
                            } else {
                                $jsonData = $jsonData1;
                                $n++;
                            }
                            break;
                    }
                    $cart = $jsonData['carts'][$n];

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
                            $pdf->textBoxPdf($ball, $this->posIni['ball_x'] + $this->posIni['ball_width'] * ((int)(($i - 1) / 5)) + $this->cartXoffSet[$cartPos - 1], ($this->posIni['ball_y'] + $this->posIni['ball_height'] * ((int) (($i - 1) % 5))) + $this->cartYoffSet[$cartPos - 1], $this->posIni['ball_width'], $this->posIni['ball_height'], $argsTxt);
                        } else {
                            // JOCKER
                            $pdf->imagePngPdf($jockerPng, $this->posIni['ball_x'] + $this->posIni['ball_width'] * ((int)(($i - 1) / 5)) + $this->cartXoffSet[$cartPos - 1] + 1, ($this->posIni['ball_y'] + $this->posIni['ball_height'] * (($i - 1) % 5)) + $this->cartYoffSet[$cartPos - 1], $this->posIni['ball_width'] - 2, $this->posIni['ball_height'] - 2);
                        }
                        $i++;
                    }

                    // HASH CART
                    $pdf->textBoxPdf(BingoCart::getHashCart($cart), $this->posIni['hash_x'] + $this->cartXoffSet[$cartPos - 1], $this->posIni['hash_y'] + $this->cartYoffSet[$cartPos - 1], 75, 5, ['font' => 'dejavusansmono', 'style' => '', 'size' => 10]);
                }
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
        $response->getBody()->write(
            json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

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


    public function createThreeSeriesOneCartTwoSideA4(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $args = $request->getParsedBody();

        $this->cartXoffSet = [0, 0, 94, 188];
        $this->cartYoffSet = [0, 0, 0, 0];

        if (!key_exists('error', $args)) {
            // PDF CREATE

            $bgFileFr = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . 'fr.jpg';
            $bgFileBk = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie'] . 'bk.jpg';
            $jockerPng = dirname(__FILE__, 3) . '\\app\\assets\\img' . DIRECTORY_SEPARATOR . $_ENV['JOCKER_JPG'];
            $jsonFile1 = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie1'] . '.json';
            $jsonFile2 = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie2'] . '.json';
            $jsonFile3 = dirname(__FILE__, 3) . $_ENV['PATH_TO_UPLOAD'] . DIRECTORY_SEPARATOR . $args['serie3'] . '.json';
            $jsonData1 = json_decode(file_get_contents($jsonFile1), true);
            $jsonData2 = json_decode(file_get_contents($jsonFile2), true);
            $jsonData3 = json_decode(file_get_contents($jsonFile3), true);

            $pdf = new BingoPdf('A4', 'L');

            $pdf->setProtection(['copy', 'modify', 'extract', 'assemble'], $_ENV['PDF_PASS'], $_ENV['PDF_OWNER_PASS'], 0, null);

            $pdf->setFilePDF($args['serie'] . '.pdf');

            if ($args['from'] <= '0') {
                $args['from'] = 1;
            }
            if ($args['to'] <= '0' or $args['to'] > count($jsonData1['carts'])) {
                $args['to'] = count($jsonData1['carts']);
            }

            $page = 0;

            for ($n = (int)$args['from'] - 1; $n < (int)$args['to']; $n++) {
                // CARTELAS
                $page++;
                // NOVA PÁGINA (FRENTE)
                $pdf->AddPage();

                // BACKGROUND IMAGE (FRENTE)
                $pdf->imageJpgPdf($bgFileFr, 0, 0, 297, 210);

                // BARCODE CART NUMBER
                $pdf->barCodePdf($args['serie'] . sprintf('%03d', $n + 1), 255, 192, 30, 10, 'I25+', 0.4, $this->barCodeStyle);

                // NOVA PÁGINA (VERSO)
                $pdf->AddPage();

                // BACKGROUND IMAGE (FRENTE)
                $pdf->imageJpgPdf($bgFileBk, 0, 0, 297, 210);

                // BARCODE SERIE 1
                $pdf->barCodePdf($args['serie1'] . sprintf('%03d', $n + 1), 16 + $this->cartXoffSet[1], 65, 30, 10, 'I25+', 0.4, $this->barCodeStyle);

                // BARCODE SERIE 2
                $pdf->barCodePdf($args['serie2'] . sprintf('%03d', $n + 1), 16 + $this->cartXoffSet[2], 65, 30, 10, 'I25+', 0.4, $this->barCodeStyle);

                // BARCODE SERIE 3
                $pdf->barCodePdf($args['serie3'] . sprintf('%03d', $n + 1), 16 + $this->cartXoffSet[3], 65, 30, 10, 'I25+', 0.4, $this->barCodeStyle);

                // CABEÇALHO SERIE 1 (TEXTO HORIZONTAL)
                $pdf->textBoxPdf($jsonData1['head']['title_serie'], 30 + $this->cartXoffSet[1], 76 + $this->cartYoffSet[1], 65, 11, ['style' => 'B', 'size' => 16]);
                $pdf->textBoxPdf($jsonData1['head']['jackpot'], 30 + $this->cartXoffSet[1], 85 + $this->cartYoffSet[1], 65, 11, []);

                // CABEÇALHO SERIE 2 (TEXTO HORIZONTAL)
                $pdf->textBoxPdf($jsonData2['head']['title_serie'], 30 + $this->cartXoffSet[2], 76 + $this->cartYoffSet[2], 65, 11, ['style' => 'B', 'size' => 16]);
                $pdf->textBoxPdf($jsonData2['head']['jackpot'], 30 + $this->cartXoffSet[2], 85 + $this->cartYoffSet[2], 65, 11, []);

                // CABEÇALHO SERIE 3 (TEXTO HORIZONTAL)
                $pdf->textBoxPdf($jsonData3['head']['title_serie'], 30 + $this->cartXoffSet[3], 76 + $this->cartYoffSet[3], 65, 11, ['style' => 'B', 'size' => 16]);
                $pdf->textBoxPdf($jsonData3['head']['jackpot'], 30 + $this->cartXoffSet[3], 85 + $this->cartYoffSet[3], 65, 11, []);

                for ($cartPos = 1; $cartPos <= 3; $cartPos++) {
                    switch ($cartPos) {
                        case 1:
                            $jsonData = $jsonData1;
                            break;
                        case 2:
                            $jsonData = $jsonData2;
                            break;
                        case 3:
                            $jsonData = $jsonData3;
                            break;
                    }
                    $cart = $jsonData['carts'][$n];

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
                            $pdf->textBoxPdf($ball, 15 + 16 * ((int)(($i - 1) / 5)) + $this->cartXoffSet[$cartPos - 1], (115 + 16 * ((int) (($i - 1) % 5))) + $this->cartYoffSet[$cartPos - 1], 16, 16, $argsTxt);
                        } else {
                            // JOCKER
                            $pdf->imagePngPdf($jockerPng, 16 + 16 * ((int)(($i - 1) / 5)) + $this->cartXoffSet[$cartPos - 1], (115 + 16 * (($i - 1) % 5)) + $this->cartYoffSet[$cartPos - 1], 14, 14);
                        }
                        $i++;
                    }

                    // HASH CART
                    $pdf->textBoxPdf(BingoCart::getHashCart($cart), 17 + $this->cartXoffSet[$cartPos - 1], 195 + $this->cartYoffSet[$cartPos - 1], 75, 5, ['font' => 'dejavusansmono', 'style' => '', 'size' => 10]);
                }
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
        $response->getBody()->write(
            json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
