<?php

use App\controllers\AppController;
use App\controllers\BingoController;
use App\controllers\TesteController;
use App\middlewares\OAuthMiddleware;
use App\controllers\UploadController;
use App\controllers\BingoPdfController;
use App\middlewares\JsonBodyParserMiddleware;
use App\middlewares\CsvFileValidatorMiddleware;
use App\middlewares\JsonBingoCartValidatorMiddleware;
use App\middlewares\BingoFiles1SideValidatorMiddleware;
use App\middlewares\BingoFiles2SideValidatorMiddleware;
use App\middlewares\BingoFiles2Side3JsonValidatorMiddleware;
use App\middlewares\JsonFileSignatureValidatorMiddleware;

// API
$app->get('/api/pix/txt', [TesteController::class, 'index'])
    ->add(new OAuthMiddleware());
$app->get('/api/teste/one/{id}', [TesteController::class, 'teste']);
$app->post('/api/bingo/create', [BingoController::class, 'create'])
    ->add(new JsonBingoCartValidatorMiddleware())
    ->add(new JsonBodyParserMiddleware());
//->add(new OAuthMiddleware());
$app->post('/api/bingo/upload/csv', [UploadController::class, 'csvToJson'])
    ->add(new CsvFileValidatorMiddleware());
$app->post('/api/bingo/upload/json', [UploadController::class, 'uploadJson'])
    ->add(new JsonFileSignatureValidatorMiddleware());
//->add(new OAuthMiddleware());
$app->post('/api/bingo/upload/background', [UploadController::class, 'uploadBack']);
//->add(new OAuthMiddleware());
$app->get('/api/bingo/pdf/generate/one_serie/two_carts/one_side/A5/{serie}/{from}/{to}', [BingoPdfController::class, 'createOneSerieTwoCartsOneSideA5'])
    ->add(new BingoFiles1SideValidatorMiddleware());
//->add(new OAuthMiddleware());
$app->get('/api/bingo/pdf/generate/one_serie/two_carts/two_side/A4/{serie}/{from}/{to}', [BingoPdfController::class, 'createOneSerieTwoCartsTwoSideA4'])
    ->add(new BingoFiles2SideValidatorMiddleware());
//->add(new OAuthMiddleware());
$app->get('/api/bingo/pdf/generate/three_series/one_cart/two_side/A4/{serie}/{from}/{to}', [BingoPdfController::class, 'createThreeSeriesOneCartTwoSideA4'])
    ->add(new BingoFiles2Side3JsonValidatorMiddleware());
//->add(new OAuthMiddleware());

// APP
$app->get('/app/bingo/upload', [AppController::class, 'bingoUpload']);
// $app->get('/app/bingo/');
