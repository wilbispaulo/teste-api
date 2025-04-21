<?php

use App\controllers\AppController;
use App\controllers\BingoController;
use App\controllers\BingoPdfController;
use App\controllers\TesteController;
use App\middlewares\OAuthMiddleware;
use App\controllers\UploadController;
use App\middlewares\JsonBodyParserMiddleware;
use App\middlewares\JsonBingoCartValidatorMiddleware;
use App\middlewares\JsonFileSignatureValidatorMiddleware;

// API
$app->get('/api/pix/txt', [TesteController::class, 'index'])->add(new OAuthMiddleware());
$app->get('/api/teste/one/{id}', [TesteController::class, 'teste']);
$app->post('/api/bingo/create', [BingoController::class, 'create'])
    ->add(new JsonBingoCartValidatorMiddleware())
    ->add(new JsonBodyParserMiddleware());
$app->post('/api/bingo/upload/json', [UploadController::class, 'uploadJson'])
    ->add(new JsonFileSignatureValidatorMiddleware());
$app->post('/api/bingo/upload/background', [UploadController::class, 'uploadBack']);
$app->post('/api/bingo/pdf/create/one_serie/two_carts', [BingoPdfController::class, 'createOneSerieTwoCarts'])
    ->add(new JsonBodyParserMiddleware());

// APP
$app->get('/app/bingo/upload', [AppController::class, 'bingoUpload']);
// $app->get('/app/bingo/');
