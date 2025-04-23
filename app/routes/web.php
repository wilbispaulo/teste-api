<?php

use App\controllers\AppController;
use App\controllers\BingoController;
use App\controllers\TesteController;
use App\middlewares\OAuthMiddleware;
use App\controllers\UploadController;
use App\controllers\BingoPdfController;
use App\middlewares\JsonBodyParserMiddleware;
use App\middlewares\BingoFilesValidatorMiddleware;
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
$app->get('/api/bingo/pdf/generate/one_serie/two_carts/{serie}', [BingoPdfController::class, 'createOneSerieTwoCarts'])
    ->add(new BingoFilesValidatorMiddleware());

// APP
$app->get('/app/bingo/upload', [AppController::class, 'bingoUpload']);
// $app->get('/app/bingo/');
